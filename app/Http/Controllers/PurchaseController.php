<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Support\DocumentNumber;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller implements HasMiddleware
{
    // Hanya Admin yang boleh akses Stok Masuk
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin'),
        ];
    }

    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('purchases.form', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date|before_or_equal:today',
            'note' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ], [
            'items.*.product_id.distinct' => 'Produk tidak boleh dipilih dua kali dalam satu dokumen.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
        ]);

        // 2. Proses Simpan dengan DB Transaction
        try {
            DB::transaction(function () use ($validated) {
                $totalAmount = 0;
                $purchaseItems = [];

                // Siapkan data item dan hitung total
                foreach ($validated['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['purchase_price'];
                    $totalAmount += $subtotal;

                    $purchaseItems[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'purchase_price' => $item['purchase_price'],
                        'subtotal' => $subtotal,
                    ];
                }

                // Simpan Header
                $purchase = Purchase::create([
                    'purchase_number' => DocumentNumber::generatePurchaseNumber(),
                    'supplier_id' => $validated['supplier_id'],
                    'user_id' => auth()->id(),
                    'purchase_date' => $validated['purchase_date'],
                    'total_amount' => $totalAmount,
                    'note' => $validated['note'] ?? null,
                ]);

                // Simpan Detail Item
                $purchase->items()->createMany($purchaseItems);

                // Update Stok Produk & Harga Modal Terbaru
                foreach ($validated['items'] as $item) {
                    $product = Product::find($item['product_id']);
                    $product->increment('stock', $item['quantity']);
                    $product->update(['purchase_price' => $item['purchase_price']]); // Opsional, update harga modal terbaru
                }
            });

            return redirect()->route('purchases.index')->with('success', 'Dokumen Stok Masuk berhasil disimpan dan stok produk telah diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }
}