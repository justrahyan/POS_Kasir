@extends('layouts.app')

@section('content')
<div class="bg-gray-50 rounded-lg p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl w-full mx-auto">

        {{-- Form Header --}}
        <div class="mb-8 text-center sm:text-left">
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">Tambah Produk Baru</h1>
            <p class="mt-1 text-sm text-gray-500">Isi detail produk pada kolom yang tersedia di bawah ini.</p>
        </div>

        {{-- Notifikasi Error --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        {{-- Form Utama --}}
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-xl shadow-lg">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                
                {{-- Kolom Kiri --}}
                <div class="space-y-6">
                    {{-- Input Nama Produk --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" id="name" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" value="{{ old('name') }}" placeholder="Contoh: Kopi Susu Gula Aren" required>
                    </div>

                    {{-- Input Kategori --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category_id" id="category_id" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Input Harga --}}
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="text" name="price" id="price" class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition" value="{{ old('price') }}" placeholder="25.000" required oninput="formatRupiah(this)">
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="space-y-6">
                    {{-- Input Gambar Produk --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                        <div id="image-upload-box" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg h-full">
                            <div id="image-preview-wrapper" class="hidden w-full text-center my-auto">
                                <img id="preview-image" src="#" alt="Pratinjau Gambar" class="mx-auto h-40 rounded-md mb-4 object-contain">
                                <button type="button" id="change-image-btn" class="text-sm font-medium text-amber-600 hover:text-amber-500">Ganti gambar</button>
                            </div>
                            <div id="upload-placeholder" class="space-y-1 text-center my-auto">
                                <i class="fa-regular fa-image mx-auto h-12 w-12 text-gray-400"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-amber-600 hover:text-amber-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-amber-500">
                                        <span>Unggah sebuah file</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">atau seret dan lepas</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 5MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-6 pt-6 border-t border-gray-200 flex flex-col sm:flex-row-reverse sm:gap-3">
                 <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-amber-500 hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition">
                    <i class="fa-solid fa-check"></i>
                    Simpan Produk
                </button>
                <a href="{{ route('products.index') }}" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-3 border border-gray-300 text-base font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition">
                    <i class="fa-solid fa-xmark"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function formatRupiah(el) {
    // Menghapus semua karakter kecuali angka
    let value = el.value.replace(/\D/g, '');
    
    // Format dengan pemisah ribuan
    if(value) {
        el.value = new Intl.NumberFormat('id-ID').format(value);
    } else {
        el.value = '';
    }
}

const imageInput = document.getElementById('image');
const imageUploadBox = document.getElementById('image-upload-box');
const previewWrapper = document.getElementById('image-preview-wrapper');
const previewImageEl = document.getElementById('preview-image');
const uploadPlaceholder = document.getElementById('upload-placeholder');
const changeImageBtn = document.getElementById('change-image-btn');

function showPreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImageEl.src = e.target.result;
        uploadPlaceholder.classList.add('hidden');
        previewWrapper.classList.remove('hidden');
    }
    reader.readAsDataURL(file);
}

// Event listener untuk input file
imageInput.addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        showPreview(file);
    }
});

// Event listener untuk tombol "Ganti gambar"
changeImageBtn.addEventListener('click', function() {
    imageInput.click();
});

// Event listener untuk drag and drop
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    imageUploadBox.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    imageUploadBox.addEventListener(eventName, () => imageUploadBox.classList.add('border-amber-500', 'bg-amber-50'), false);
});

['dragleave', 'drop'].forEach(eventName => {
    imageUploadBox.addEventListener(eventName, () => imageUploadBox.classList.remove('border-amber-500', 'bg-amber-50'), false);
});

imageUploadBox.addEventListener('drop', function(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files.length > 0) {
        // Assign file ke input agar bisa di-submit oleh form
        imageInput.files = files;
        showPreview(files[0]);
    }
}, false);
</script>
@endsection
