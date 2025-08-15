<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data untuk Kartu Ringkasan
        $totalPendapatanHariIni = Transaction::whereDate('created_at', today())->sum('total');
        $jumlahTransaksiHariIni = Transaction::whereDate('created_at', today())->count();
        $produkTerjualHariIni = TransactionDetail::whereHas('transaction', function ($query) {
            $query->whereDate('created_at', today());
        })->sum('qty');

        // 2. Data untuk Transaksi Terakhir
        $transaksiTerakhir = Transaction::with('details.product')->latest()->take(5)->get();

        // 3. Data untuk Grafik Pendapatan 7 Hari Terakhir
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(6);

        $salesData = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date');

        // Siapkan array untuk label dan data grafik
        $chartLabels = [];
        $chartData = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d M'); // Format label (e.g., 14 Aug)
            $chartData[] = $salesData[$formattedDate] ?? 0; // Isi dengan 0 jika tidak ada penjualan
        }

        return view('dashboard', compact(
            'totalPendapatanHariIni',
            'jumlahTransaksiHariIni',
            'produkTerjualHariIni',
            'transaksiTerakhir',
            'chartLabels',
            'chartData'
        ));
    }
}
