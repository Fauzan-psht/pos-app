<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('sku', 'like', '%'.$request->search.'%');
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
        $units = collect(Product::UNIT_OPTIONS)
            ->merge($products->getCollection()->pluck('unit'))
            ->filter()
            ->unique()
            ->values();

        return view('products.index', compact('products', 'categories', 'units'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Product::UNIT_OPTIONS;

        return view('products.form', compact('categories', 'units'));
    }

    public function store(Request $request, ImageService $imageService)
    {
        // Validasi Server-Side
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:50|alpha_dash|unique:products,sku',
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|string', // Ubah jadi string karena ada titik
            'selling_price' => 'required|string',  // Ubah jadi string karena ada titik
            'stock' => 'required|numeric|decimal:0,3|min:0',
            'min_stock' => 'required|numeric|decimal:0,3|min:0',
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
            $validated['image'] = $imageService->storeProductImage($request->file('image'));
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
        $units = collect(Product::UNIT_OPTIONS)->push($product->unit)->filter()->unique()->values();

        return view('products.form', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product, ImageService $imageService)
    {
        // Validasi Server-Side
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('products')->ignore($product->id)],
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'required|string',
            'selling_price' => 'required|string',
            'min_stock' => 'required|numeric|decimal:0,3|min:0',
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

        $oldImagePath = null;
        if ($request->hasFile('image')) {
            $oldImagePath = $product->image;
            $validated['image'] = $imageService->storeProductImage($request->file('image'));
        }

        $product->update($validated);
        $imageService->deleteProductImage($oldImagePath);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product, ImageService $imageService)
    {
        // Cek apakah produk sudah punya riwayat transaksi
        $hasTransactions = SaleItem::where('product_id', $product->id)->exists() ||
                           PurchaseItem::where('product_id', $product->id)->exists();

        if ($hasTransactions) {
            return redirect()->route('products.index')->with('error', 'Produk sudah memiliki riwayat transaksi, silakan nonaktifkan saja (ubah status menjadi Tidak Aktif).');
        }

        // Hapus gambar jika ada
        $imageService->deleteProductImage($product->image);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
