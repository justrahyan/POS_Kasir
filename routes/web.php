<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Produk
    Route::resource('products', ProductController::class);

    // Kategori Produk
    Route::resource('categories', CategoryController::class);

    // Kasir
    Route::get('kasir', [TransactionController::class, 'index'])->name('kasir.index');
    Route::post('kasir', [TransactionController::class, 'store'])->name('kasir.store');

    // Payment Midtrans
    Route::post('/payment/midtrans/charge', [PaymentController::class, 'charge'])->name('payment.midtrans.charge');

    // Laporan
    Route::get('laporan', [TransactionController::class, 'laporan'])->name('laporan.index');

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/', [SettingController::class, 'store'])->name('settings.store');
    });
});

Route::post('/payment/midtrans/notification', [PaymentController::class, 'notificationHandler'])->name('payment.midtrans.notification');

require __DIR__.'/auth.php';
