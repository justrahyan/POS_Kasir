@extends('layouts.errors-layout')

@section('title', __('Not Found'))

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-center px-4">
        
        <div class="mb-6">
            <svg class="w-16 h-16 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>

        <h1 class="text-5xl font-bold text-amber-500">404</h1>
        <h2 class="mt-4 text-2xl font-semibold text-gray-800 tracking-tight">Halaman Tidak Ditemukan</h2>
        <p class="mt-2 text-base text-gray-600 max-w-sm">
            Maaf, kami tidak dapat menemukan halaman yang Anda cari. Mungkin URL-nya salah ketik atau halamannya sudah dipindahkan.
        </p>

        <div class="mt-8">
            <a href="{{ url('/dashboard') }}" 
               class="inline-block px-6 py-3 bg-amber-500 text-white font-semibold rounded-lg shadow-md hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200">
                Kembali ke Dashboard
            </a>
        </div>

    </div>
@endsection