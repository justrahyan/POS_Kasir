@extends('layouts.app')

@section('content')
<div class="bg-gray-50 rounded-lg p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">

        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8">
            <div>
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Kategori</h1>
                <p class="mt-1 text-sm text-gray-500">Tambah, edit, dan hapus kategori produk Anda di sini.</p>
            </div>
            <a href="{{ route('products.index') }}" 
               class="mt-4 sm:mt-0 inline-flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Produk</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Kolom Kiri: Form Tambah/Edit --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
                    @if(isset($category))
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Kategori</h3>
                        <form action="{{ route('categories.update', $category->id) }}" method="POST">
                            @method('PUT')
                    @else
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Kategori Baru</h3>
                        <form action="{{ route('categories.store') }}" method="POST">
                    @endif
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                                       placeholder="Contoh: Makanan Berat">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex gap-3">
                                @if(isset($category))
                                    <a href="{{ route('categories.index') }}" class="w-full text-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Batal</a>
                                @endif
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-500 hover:bg-amber-600">
                                    {{ isset($category) ? 'Simpan' : 'Tambah Kategori' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Kolom Kanan: Daftar Kategori --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200/80">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produk</th>
                                <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categories as $cat)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cat->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cat->products_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex gap-3 justify-end">
                                    <a href="{{ route('categories.edit', $cat->id) }}" class="text-amber-600 hover:text-amber-900">Edit</a>
                                    <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-500">
                                    Belum ada kategori.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
