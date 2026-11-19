<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class CategoryController extends Controller implements HasMiddleware
{
    // Satpam: Hanya admin yang boleh masuk
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin', except: ['index']),
        ];
    }

    public function index(Request $request)
    {
        // Eager loading denganCount untuk menghitung jumlah produk
        $query = Category::withCount('products');

        // Fitur Pencarian
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Menampilkan 10 data terbaru per halaman
        $categories = $query->latest()->paginate(10)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    // Method create dan show tidak diperlukan lagi karena kita pakai Modal (Pop-up)
    // Tapi kita biarkan saja untuk berjaga-jaga
    public function create()
    {
        return view('categories.form');
    }

    public function store(Request $request)
    {
        // Validasi Server-Side
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah ada.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // Validasi Server-Side (Ignore ID saat ini agar tidak dianggap duplikat)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah ada.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Aturan Bisnis: Cek apakah kategori masih dipakai oleh produk
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Kategori masih dipakai oleh ' . $category->products()->count() . ' produk, tidak bisa dihapus.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}