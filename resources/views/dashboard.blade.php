@extends('layouts.app')

@section('content')
{{-- Impor Chart.js untuk grafik --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="bg-gray-50 rounded-lg p-4 sm:p-6 lg:p-8">
    <div class="space-y-8">
        {{-- Header Selamat Datang --}}
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-sm text-gray-600">Berikut adalah ringkasan aktivitas toko Anda hari ini.</p>
        </div>

        {{-- Kartu Ringkasan Hari Ini --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">Rp{{ number_format($totalPendapatanHariIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg"><i class="fa-solid fa-arrow-up-right-dots fa-lg"></i></div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Transaksi Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $jumlahTransaksiHariIni }}</p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg"><i class="fa-solid fa-receipt fa-lg"></i></div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Produk Terjual Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $produkTerjualHariIni }}</p>
                    </div>
                    <div class="bg-amber-100 text-amber-600 p-3 rounded-lg"><i class="fa-solid fa-box-archive fa-lg"></i></div>
                </div>
            </div>
        </div>

        {{-- Grafik dan Transaksi Terakhir --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            {{-- Grafik Pendapatan --}}
            <div class="xl:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200/80">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pendapatan 7 Hari Terakhir</h3>
                <div class="h-80">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- Transaksi Terakhir --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">Transaksi Terakhir</h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($transaksiTerakhir as $trx)
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 flex-shrink-0 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-receipt text-gray-500"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-800">INV-{{ $trx->id }}</p>
                                <p class="text-xs text-gray-500">{{ $trx->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600">+Rp{{ number_format($trx->total, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ $trx->details->sum('qty') }} item</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">Belum ada transaksi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pendapatan',
                data: @json($chartData),
                backgroundColor: 'rgba(245, 158, 11, 0.6)', // amber-500 with opacity
                borderColor: 'rgba(245, 158, 11, 1)', // amber-500
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
