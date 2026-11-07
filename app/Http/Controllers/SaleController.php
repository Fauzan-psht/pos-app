<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Support\DocumentNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            $query->whereBetween('sale_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->filled('invoice')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice . '%');
        }

        $sales = $query->paginate(10)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
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

            // BERHASIL: Arahkan ke halaman Struk (sales.show)
            return redirect()->route('sales.show', $sale->id)->with('success', 'Transaksi berhasil disimpan.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
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
}