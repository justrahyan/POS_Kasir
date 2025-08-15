@extends('layouts.app')

@section('content')
<div class="bg-gray-50 rounded-lg p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">

        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4">
            <div>
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 tracking-tight">Manajemen Produk</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola, tambah, dan perbarui daftar produk Anda di sini.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-3">
                <a href="{{ route('categories.index') }}" 
                   class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    <i class="fa-solid fa-tags"></i>
                    <span>Kategori</span>
                </a>
                <a href="{{ route('products.create') }}" 
                   class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-medium px-4 py-2 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Produk</span>
                </a>
            </div>
        </div>

        {{-- Filter dan Pencarian --}}
        <div class="mb-8">
            <form action="{{ route('products.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <div class="relative">
                            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" placeholder="Cari produk berdasarkan nama..." value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div>
                        <select name="category" onchange="this.form.submit()" class="w-full py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>


        {{-- Grid Daftar Produk --}}
        @if($products->isEmpty())
            <div class="text-center py-16 bg-white rounded-lg shadow-sm">
                 <i class="fa-solid fa-box-open text-4xl text-gray-300"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Produk</h3>
                <p class="mt-1 text-sm text-gray-500">Produk akan muncul di sini.</p>
                <!-- <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center text-sm font-medium text-amber-600 hover:text-amber-500">
                    Reset Pencarian
                </a> -->
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                <div class="group relative bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-amber-300 flex flex-col">
                    
                    <div class="relative">
                        {{-- Badge Kategori --}}
                        @if($product->category)
                            <span class="absolute top-2 left-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                {{ $product->category->name }}
                            </span>
                        @endif
                        
                        {{-- Gambar Produk --}}
                        <div class="h-52 w-full overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition-transform duration-300 md:group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                    <i class="fa-regular fa-image text-4xl text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Konten Card --}}
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="text-base font-semibold text-gray-800 line-clamp-2 h-12">
                            <p>
                                {{ $product->name }}
                            </p>
                        </h3>
                        <div class="mt-auto">
                            <p class="mt-2 text-xl font-bold text-amber-500">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="absolute top-2 right-2 flex flex-col gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">
                        <a href="{{ route('products.edit', $product->id) }}" class="h-9 w-9 flex items-center justify-center bg-white/80 backdrop-blur-sm text-gray-600 hover:bg-yellow-400 hover:text-white rounded-full shadow-md transition-all duration-200" title="Edit Produk">
                           <i class="fa-solid fa-pencil text-sm"></i>
                        </a>
                        <button type="button" class="delete-btn h-9 w-9 flex items-center justify-center bg-white/80 backdrop-blur-sm text-gray-600 hover:bg-red-500 hover:text-white rounded-full shadow-md transition-all duration-200" title="Hapus Produk" data-action="{{ route('products.destroy', $product->id) }}" data-product-name="{{ $product->name }}">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="delete-modal" class="px-4 md:px-0 fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fa-solid fa-trash-can text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modal-title">Hapus Produk</h3>
            <div class="mt-2 px-7 py-3 flex flex-col gap-1">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus produk
                </p>
                <p class="font-bold"><span id="product-name-in-modal" class="text-gray-700"></span> ?</p>
                <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="delete-form" action="" method="POST" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button id="confirm-delete-btn" type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Ya, Hapus
                    </button>
                </form>
                <button id="cancel-delete-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('delete-modal');
    const deleteForm = document.getElementById('delete-form');
    const productNameInModal = document.getElementById('product-name-in-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const deleteButtons = document.querySelectorAll('.delete-btn');

    // Fungsi untuk menampilkan modal
    function openModal(action, productName) {
        deleteForm.action = action;
        productNameInModal.textContent = productName;
        deleteModal.classList.remove('hidden');
    }

    // Fungsi untuk menyembunyikan modal
    function closeModal() {
        deleteModal.classList.add('hidden');
    }

    // Tambahkan event listener ke semua tombol hapus
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah aksi default
            const action = this.dataset.action;
            const productName = this.dataset.productName;
            openModal(action, productName);
        });
    });

    // Tambahkan event listener ke tombol batal
    cancelDeleteBtn.addEventListener('click', closeModal);

    // Tambahkan event listener untuk menutup modal jika klik di luar area modal
    deleteModal.addEventListener('click', function(event) {
        if (event.target === deleteModal) {
            closeModal();
        }
    });
});
</script>
@endsection
