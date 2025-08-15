@extends('layouts.app')

@section('content')
<div class="bg-gray-50 rounded-lg p-4 sm:p-6 lg:p-8">
    <div class="space-y-6">
        {{-- Header Halaman --}}
        <div class="mb-8">
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 tracking-tight">Profil Akun</h1>
            <p class="mt-1 text-sm text-gray-600">Perbarui informasi profil dan alamat email akun Anda.</p>
        </div>

        {{-- Form Informasi Profil --}}
        <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-200/80">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Form Update Password --}}
        <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-200/80">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Form Hapus Akun --}}
        <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-200/80">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
