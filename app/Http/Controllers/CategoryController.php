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
            new Middleware('role:admin'),
        ];
    }

    public function index(Request $request)
    {
        $query = Category::withCount('products');
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(10)->withQueryString();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:255',
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($validated);
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Cek aturan bisnis: kalau punya produk, jangan bisa dihapus
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Kategori masih dipakai oleh produk, tidak bisa dihapus.');
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}