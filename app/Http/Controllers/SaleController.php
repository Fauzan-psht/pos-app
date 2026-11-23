<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Support\DocumentNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // Kasir hanya lihat miliknya sendiri, admin lihat semua
        $query = Sale::with(['user', 'customer'])->latest();

        if (auth()->user()->role === 'kasir') {
            $query->where('user_id', auth()->id());
        }

        // Filter Tanggal & Invoice
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sale_date', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
        }
        if ($request->filled('invoice')) {
            $query->where('invoice_number', 'like', '%'.$request->invoice.'%');
        }

        if ($request->ajax()) {
            return DataTables::of($query)
                ->editColumn('sale_date', fn (Sale $sale) => Carbon::parse($sale->sale_date)->format('d/m/Y H:i'))
                ->addColumn('customer_name', fn (Sale $sale) => $sale->customer?->name ?? 'Umum')
                ->editColumn('total', fn (Sale $sale) => number_format((float) $sale->total, 0, ',', '.'))
                ->editColumn('status', fn (Sale $sale) => $sale->status === 'paid'
                    ? '<span class="badge bg-success">Sukses</span>'
                    : '<span class="badge bg-danger">Dibatalkan</span>')
                ->addColumn('action', fn (Sale $sale) => '<a href="'.route('sales.show', $sale->id).'" class="btn btn-sm btn-outline-secondary" title="Detail"><i class="bi bi-eye"></i> Detail</a>')
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $sales = $query->paginate(10)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        // AMBIL DATA PRODUK & PELANGGAN
        $customers = Customer::orderBy('name')->get();

        return view('sales.create', compact('customers'));
    }

    public function products(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $products = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return response()->json([
            'data' => $products->getCollection()->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'selling_price' => (float) $product->selling_price,
                'stock' => (float) $product->stock,
                'image_url' => $product->image ? Storage::disk('public')->url($product->image) : null,
            ]),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        foreach (['discount', 'paid_amount'] as $amountField) {
            $amount = $request->input($amountField);

            if (is_string($amount)) {
                $request->merge([$amountField => str_replace('.', '', $amount)]);
            }
        }

        // 1. Validasi Input Dasar
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.quantity' => 'required|numeric|decimal:0,3|min:0.001',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:tunai,qris,transfer',
            'paid_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        // 2. Proses Simpan dengan DB Transaction & Locking
        try {
            $sale = DB::transaction(function () use ($validated) {
                $ids = collect($validated['items'])->pluck('product_id');

                // Kunci baris produk agar tidak bisa diubah proses lain selama transaksi ini
                $products = Product::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

                $subtotal = 0;
                $saleItems = [];

                // Hitung ulang total di server (Jangan percaya angka dari browser)
                foreach ($validated['items'] as $index => $item) {
                    $product = $products[$item['product_id']];

                    // Cek stok
                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            "items.$index.quantity" => "Stok {$product->name} tidak cukup (sisa {$product->stock}).",
                        ]);
                    }

                    $itemSubtotal = $product->selling_price * $item['quantity'];
                    $subtotal += $itemSubtotal;

                    $saleItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->selling_price,
                        'purchase_price' => $product->purchase_price, // Snapshot modal untuk laba
                        'quantity' => $item['quantity'],
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $discount = $validated['discount'] ?? 0;
                if ($discount > $subtotal) {
                    throw ValidationException::withMessages(['discount' => 'Diskon tidak boleh melebihi subtotal.']);
                }

                $total = $subtotal - $discount;
                $paidAmount = $validated['payment_method'] === 'tunai' ? $validated['paid_amount'] : $total;

                if ($paidAmount < $total) {
                    throw ValidationException::withMessages(['paid_amount' => 'Uang bayar kurang dari total.']);
                }

                $changeAmount = $paidAmount - $total;

                // Simpan Header
                $newSale = Sale::create([
                    'invoice_number' => DocumentNumber::generateInvoiceNumber(),
                    'user_id' => auth()->id(),
                    'customer_id' => $validated['customer_id'] ?? null,
                    'sale_date' => now(),
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'payment_method' => $validated['payment_method'],
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                    'status' => 'paid',
                    'note' => $validated['note'] ?? null,
                ]);

                // Simpan Detail Item
                $newSale->items()->createMany($saleItems);

                // Kurangi Stok
                foreach ($validated['items'] as $item) {
                    Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
                }

                return $newSale;
            });

            // BERHASIL: Arahkan ke halaman Struk
            return redirect()->route('sales.show', $sale->id)->with('success', 'Transaksi berhasil disimpan.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'Transaksi gagal diproses. Silakan coba lagi atau hubungi admin.')->withInput();
        }
    }

    public function show(Sale $sale)
    {
        // Proteksi: Kasir tidak boleh lihat transaksi kasir lain
        if (auth()->user()->role === 'kasir' && $sale->user_id !== auth()->id()) {
            abort(403);
        }

        $sale->load(['items', 'customer', 'user']);

        return view('sales.receipt', compact('sale'));
    }

    public function detail(Sale $sale)
    {
        // Proteksi: Kasir tidak boleh lihat transaksi kasir lain
        if (auth()->user()->role === 'kasir' && $sale->user_id !== auth()->id()) {
            abort(403);
        }

        $sale->load(['items', 'customer', 'user']);

        return view('sales.detail', compact('sale'));
    }

    public function void(Request $request, Sale $sale)
    {
        // 1. Hanya Admin yang bisa akses
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang bisa membatalkan transaksi.');
        }

        // 2. Cek jika sudah void
        if ($sale->status === 'void') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        // 3. Validasi alasan
        $request->validate([
            'void_reason' => 'required|string|max:255',
        ]);

        // 4. Proses DB Transaction
        try {
            DB::transaction(function () use ($sale, $request) {
                $sale->update([
                    'status' => 'void',
                    'note' => 'VOID: '.$request->void_reason,
                ]);

                foreach ($sale->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            });

            return redirect()->route('sales.index')->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: '.$e->getMessage());
        }
    }
}
