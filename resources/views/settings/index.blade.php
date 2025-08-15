@extends('layouts.app')

@section('content')
<div class="bg-gray-50 rounded-lg p-4 sm:p-6 lg:p-8">
    <div class="space-y-6">
        {{-- Header Halaman --}}
        <div class="mb-8">
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 tracking-tight">Pengaturan Toko</h1>
            <p class="mt-1 text-sm text-gray-600">Atur informasi dasar toko Anda yang akan ditampilkan pada struk.</p>
        </div>

        {{-- Form Pengaturan --}}
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-gray-200/80">
            <div class="max-w-2xl">
                <form method="POST" action="{{ route('settings.store') }}" class="space-y-6">
                    @csrf

                    {{-- Input Nama Toko --}}
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
                        <input type="text" id="store_name" name="store_name" value="{{ $settings['store_name'] ?? '' }}" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                            placeholder="Contoh: Cemilan Ibu Yana">
                    </div>

                    {{-- Input Alamat Toko --}}
                    <div>
                        <label for="store_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Toko</label>
                        <textarea id="store_address" name="store_address" rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                                placeholder="Jl. Merdeka No. 17, Makassar">{{ $settings['store_address'] ?? '' }}</textarea>
                    </div>

                    {{-- Input Nomor Telepon --}}
                    <div>
                        <label for="store_phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" id="store_phone" name="store_phone" value="{{ $settings['store_phone'] ?? '' }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                            placeholder="0812-3456-7890">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fa-solid fa-save"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
