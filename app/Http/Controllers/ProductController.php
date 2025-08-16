<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\TransactionDetail;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource with filtering and search.
     */
    public function index(Request $request)
    {
        // Mulai query builder untuk produk dan eager load relasi kategori
        $query = Product::query()->with('category');

        // Terapkan filter pencarian jika ada
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Terapkan filter kategori jika ada
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Ambil hasil produk yang sudah difilter dan diurutkan
        $products = $query->latest()->get();

        // Ambil semua kategori untuk ditampilkan di dropdown filter
        $categories = Category::orderBy('name')->get();

        // Kirim data ke view
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        // Ambil kategori untuk ditampilkan di form create
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // hapus titik ribuan sebelum validasi
        $request->merge([
            'price' => str_replace('.', '', $request->price)
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $data = $request->only(['name', 'price', 'category_id']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        // Ambil kategori untuk ditampilkan di form edit
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'price' => str_replace('.', '', $request->price)
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $product = Product::findOrFail($id);
        $data = $request->only(['name', 'price', 'category_id']);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        TransactionDetail::where('product_id', $product->id)->update([
            'product_image' => null
        ]);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }
}
