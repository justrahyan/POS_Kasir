@extends('layouts.app')

@section('content')
{{-- Impor Alpine.js untuk interaktivitas (expand/collapse detail) --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-gray-50 rounded-lg p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 tracking-tight">Laporan Transaksi</h1>
            <p class="mt-1 text-sm text-gray-600">Ringkasan dan riwayat semua transaksi yang telah tercatat.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">Rp{{ number_format($transactions->sum('total'), 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg">
                        <i class="fa-solid fa-arrow-up-right-dots fa-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Jumlah Transaksi</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $transactions->count() }}</p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                        <i class="fa-solid fa-receipt fa-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Rata-rata / Transaksi</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">Rp{{ number_format($transactions->avg('total'), 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-amber-100 text-amber-600 p-3 rounded-lg">
                        <i class="fa-solid fa-scale-balanced fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200/80">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('laporan.index', ['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-start gap-2">
                                    No. Invoice
                                    <i class="fa-solid @if(request('sort') == 'id') {{ request('direction') == 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} @else fa-sort-down @endif"></i>
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('laporan.index', ['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-start gap-2">
                                    Tanggal
                                    <i class="fa-solid @if(request('sort') == 'created_at') {{ request('direction') == 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} @else fa-sort-down @endif"></i>
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('laporan.index', ['sort' => 'total', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-start gap-2">
                                    Total
                                    <i class="fa-solid @if(request('sort') == 'total') {{ request('direction') == 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} @else fa-sort-down @endif"></i>
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                                <div x-data="{ filterOpen: false }" @click.away="filterOpen = false" class="relative inline-block text-left">
                                    <button @click="filterOpen = !filterOpen" type="button" class="group flex items-start gap-2 uppercase">
                                        Metode Pembayaran
                                        <i class="fa-solid fa-sort-down text-gray-500"></i>
                                    </button>
                                    <div x-show="filterOpen" x-transition class="absolute left-0 z-10 mt-2 w-40 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak>
                                        <div class="py-1">
                                            <a href="{{ route('laporan.index', request()->except(['metode', 'page'])) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 {{ !request('metode') ? 'font-bold bg-gray-50' : '' }}">
                                                Semua
                                            </a>
                                            <a href="{{ route('laporan.index', array_merge(request()->except(['metode', 'page']), ['metode' => 'cash'])) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 {{ request('metode') == 'cash' ? 'font-bold bg-gray-50' : '' }}">
                                                Tunai
                                            </a>
                                            <a href="{{ route('laporan.index', array_merge(request()->except(['metode', 'page']), ['metode' => 'qris'])) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 {{ request('metode') == 'qris' ? 'font-bold bg-gray-50' : '' }}">
                                                QRIS
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Detail</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" x-data="{ open: null }">
                        @forelse ($transactions as $trx)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">INV-{{ $trx->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-semibold">Rp{{ number_format($trx->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($trx->metode_pembayaran == 'cash')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tunai</span>
                                    @elseif($trx->metode_pembayaran == 'qris')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">QRIS</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($trx->metode_pembayaran ?: 'N/A') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="open = open === {{ $trx->id }} ? null : {{ $trx->id }}" class="text-amber-600 hover:text-amber-900">
                                        <span x-show="open !== {{ $trx->id }}">Lihat Detail</span>
                                        <span x-show="open === {{ $trx->id }}">Tutup</span>
                                    </button>
                                </td>
                            </tr>
                            <tr x-show="open === {{ $trx->id }}" x-cloak x-transition>
                                <td colspan="5" class="p-0">
                                    <div class="p-4 bg-gray-50">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-semibold text-sm">Detail Item:</h4>
                                            <span class="text-sm font-medium text-gray-600">{{ $trx->details->count() }} Item</span>
                                        </div>
                                        <ul class="divide-y divide-gray-200">
                                            @foreach($trx->details as $detail)
                                            <li class="py-2 flex justify-between items-center">
                                                <div class="flex items-center gap-3">
                                                    @if ($detail->product_image)
                                                        <img 
                                                            src="{{ asset('storage/' . $detail->product_image) }}" 
                                                            alt="{{ $detail->product_name ?? 'Produk Dihapus' }}" 
                                                            class="w-10 h-10 object-cover rounded-md">
                                                    @else
                                                        <div class="w-10 h-10 flex items-center justify-center bg-gray-200 rounded-md">
                                                            <i class="fa-solid fa-image text-gray-500 text-2xl"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">{{ $detail->product_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $detail->qty }} x Rp{{ number_format($detail->price, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-700">Rp{{ number_format($detail->qty * $detail->price, 0, ',', '.') }}</p>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-16">
                                    <i class="fa-solid fa-receipt text-4xl text-gray-300"></i>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Transaksi</h3>
                                    <p class="mt-1 text-sm text-gray-500">Riwayat transaksi akan muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection