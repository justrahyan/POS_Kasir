<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'POS Kasir') }}</title>

    {{-- Font & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js untuk interaktivitas --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io//apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io//favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io//favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon_io//site.webmanifest') }}">
</head>
<body class="h-full antialiased font-jakarta">
    <div x-data="{ open: false }" class="min-h-screen bg-gray-100">
        <!-- Off-canvas menu untuk mobile, show/hide berdasarkan state menu -->
        <div x-show="open" class="relative z-40 md:hidden" role="dialog" aria-modal="true">
            <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
            <div class="fixed inset-0 z-40 flex">
                <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button @click="open = false" type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Close sidebar</span>
                            <i class="fa-solid fa-xmark text-white"></i>
                        </button>
                    </div>
                    <div class="flex flex-shrink-0 items-center px-4">
                        <img src="{{ asset('images/cashier.svg') }}" class="h-6 w-6 md:h-8 md:w-8" alt="">
                        <span class="ml-3 text-xl font-bold text-gray-800">POS Kasir</span>
                    </div>
                    <div class="mt-5 h-0 flex-1 overflow-y-auto">
                        <nav class="space-y-1 px-2">
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <i class="fa-solid fa-house mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Dashboard
                            </a>
                            <a href="{{ route('kasir.index') }}" class="{{ request()->routeIs('kasir.index') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <i class="fa-solid fa-cash-register mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Order
                            </a>
                            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <i class="fa-solid fa-box-archive mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Produk
                            </a>
                            <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.index') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <i class="fa-solid fa-chart-line mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Laporan
                            </a>
                            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.index') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-base font-medium rounded-md">
                                <i class="fa-solid fa-store mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Pengaturan Toko
                            </a>
                        </nav>
                    </div>
                </div>
                <div class="w-14 flex-shrink-0"></div>
            </div>
        </div>

        <!-- Sidebar statis untuk desktop -->
        <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
            <div class="flex flex-grow flex-col overflow-y-auto border-r border-gray-200 bg-white pt-5">
                <div class="flex flex-shrink-0 items-center px-4">
                    <img src="{{ asset('images/cashier.svg') }}" class="h-8 w-8" alt="">
                    <span class="ml-3 text-2xl font-bold text-gray-800">POS Kasir</span>
                </div>
                <div class="mt-8 flex flex-grow flex-col">
                    <nav class="flex-1 space-y-1 px-2 pb-4">
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-3 text-sm font-medium rounded-md">
                            <i class="fa-solid fa-house mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Dashboard
                        </a>
                        <a href="{{ route('kasir.index') }}" class="{{ request()->routeIs('kasir.index') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-3 text-sm font-medium rounded-md">
                            <i class="fa-solid fa-cash-register mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Order
                        </a>
                        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-3 text-sm font-medium rounded-md">
                            <i class="fa-solid fa-box-archive mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Produk
                        </a>
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.index') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-3 text-sm font-medium rounded-md">
                            <i class="fa-solid fa-chart-line mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Laporan
                        </a>
                        <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.index') ? 'nav-link-active' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-3 text-sm font-medium rounded-md">
                            <i class="fa-solid fa-store mr-3 flex flex-shrink-0 items-center h-6 w-6"></i> Pengaturan Toko
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <div class="flex flex-1 flex-col md:pl-64">
            <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow-sm">
                <button @click="open = true" type="button" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-amber-500 md:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fa-solid fa-bars h-6 w-6"></i>
                </button>
                <div class="flex flex-1 justify-end px-4">
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Profile dropdown -->
                        <div x-data="{ profileOpen: false }" class="relative ml-3">
                            <div>
                                <button @click="profileOpen = !profileOpen" type="button" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=FBBF24&color=78350F" alt="">
                                </button>
                            </div>
                            <div x-show="profileOpen" @click.away="profileOpen = false" x-transition class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Profil Anda</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-1">
                <div class="py-6">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
                        {{-- Toast Notification --}}
                        @if (session('success'))
                            <div id="toast-success" class="fixed top-20 right-5 z-50 flex items-center w-auto max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-lg border border-green-400" role="alert">
                                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 01.083 1.32l-.083.094L8.414 15l-4.707-4.707a1 1 0 011.32-1.497l.094.083L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <div class="ml-3 text-sm font-normal">{{ session('success') }}</div>
                                <button onclick="document.getElementById('toast-success').remove()" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8" aria-label="Close">
                                    <span class="sr-only">Close</span>✕
                                </button>
                            </div>
                            <script>
                            setTimeout(() => {
                                const toast = document.getElementById('toast-success');
                                if (toast) toast.remove();
                            }, 3000);
                            </script>
                        @endif
                        
                        {{-- Konten utama dari setiap halaman akan muncul di sini --}}
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
