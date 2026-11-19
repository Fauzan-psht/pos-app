<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller implements HasMiddleware
{
    // Aturan Hak Akses: Kasir hanya boleh melihat (index, show). Admin bisa semuanya.
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        // Eager loading 'category' untuk mencegah query N+1
        $query = Product::with('category');

        // Filter Pencarian Server-Side (Jika dibutuhkan)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter Status Aktif
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'aktif');
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get(); // <-- Wajib untuk Modal Edit

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validasi Server-Side
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|alpha_dash|unique:products,sku',
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|string', // Ubah jadi string karena ada titik
            'selling_price' => 'required|string',  // Ubah jadi string karena ada titik
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        // HAPUS TITIK SEBELUM DISIMPAN
        $validated['purchase_price'] = str_replace('.', '', $request->purchase_price);
        $validated['selling_price'] = str_replace('.', '', $request->selling_price);

        // Cek Harga Jual vs Harga Modal
        if ($validated['selling_price'] < $validated['purchase_price']) {
            return back()->withErrors(['selling_price' => 'Harga jual tidak boleh lebih rendah dari harga modal.'])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active');

        // Handle Upload Gambar
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // Validasi Server-Side
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('products')->ignore($product->id)],
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|string',
            'selling_price' => 'required|string',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        // HAPUS TITIK SEBELUM DISIMPAN
        $validated['purchase_price'] = str_replace('.', '', $request->purchase_price);
        $validated['selling_price'] = str_replace('.', '', $request->selling_price);

        // Cek Harga Jual vs Harga Modal
        if ($validated['selling_price'] < $validated['purchase_price']) {
            return back()->withErrors(['selling_price' => 'Harga jual tidak boleh lebih rendah dari harga modal.'])->withInput();
        }

        $validated['is_active'] = $request->boolean('is_active');

        // Handle Upload Gambar (Hapus gambar lama jika ada gambar baru)
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Cek apakah produk sudah punya riwayat transaksi
        $hasTransactions = \App\Models\SaleItem::where('product_id', $product->id)->exists() || 
                           \App\Models\PurchaseItem::where('product_id', $product->id)->exists();

        if ($hasTransactions) {
            return redirect()->route('products.index')->with('error', 'Produk sudah memiliki riwayat transaksi, silakan nonaktifkan saja (ubah status menjadi Tidak Aktif).');
        }

        // Hapus gambar jika ada
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}