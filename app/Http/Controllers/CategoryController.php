<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        // Form create ada di halaman index, jadi method ini tidak perlu
        return redirect()->route('categories.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($request->only('name'));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(Category $category)
    {
        // Tidak digunakan
        return redirect()->route('categories.index');
    }

    public function edit(Category $category)
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('categories.index', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->only('name'));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Cek jika kategori masih digunakan oleh produk
        if ($category->products()->count() > 0) {
            return back()->withErrors(['name' => 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.']);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
