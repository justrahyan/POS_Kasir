<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans dari file config
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Membuat transaksi di Midtrans dan mendapatkan Snap Token.
     */
    public function charge(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric|min:1',
            'items' => 'required|array',
        ]);

        // Buat ID unik untuk transaksi/order
        $orderId = 'INV-' . time();

        // Siapkan parameter untuk Midtrans Snap
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $request->total,
            ],
            'item_details' => $request->items,
            'customer_details' => [
                'first_name' => "Pelanggan",
                'last_name' => "Toko Ibu Yana",
                'email' => "pelanggan@tokoyana.com",
                'phone' => "08123456789",
            ]
        ];

        try {
            // Dapatkan Snap Token
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat transaksi pembayaran.'], 500);
        }
    }

    /**
     * Menerima notifikasi dari Midtrans (webhook).
     */
    public function notificationHandler(Request $request)
    {
        // 1. Dapatkan Server Key dari config
        $serverKey = config('midtrans.server_key');

        // 2. Buat hash signature untuk verifikasi
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        // 3. Verifikasi signature (keamanan)
        if ($hashed != $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 4. Lanjutkan jika signature valid
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status;

        Log::info("Notifikasi Midtrans diterima untuk Order ID {$orderId}: status={$transactionStatus}");

        $transaction = Transaction::where('invoice_number', $orderId)->first();

        if ($transaction) {
            // Jangan proses notifikasi yang sudah diproses sebelumnya
            if ($transaction->status === 'paid' || $transaction->status === 'settlement') {
                return response()->json(['message' => 'Transaction already processed']);
            }

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'accept') {
                    $transaction->update(['status' => 'paid']);
                    Log::info("Transaksi {$orderId} berhasil diupdate ke 'paid'.");
                }
            } else if ($transactionStatus == 'pending') {
                $transaction->update(['status' => 'pending']);
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $transaction->update(['status' => 'failed']);
            }
            
            return response()->json(['message' => 'Notifikasi berhasil diproses.']);
        } else {
            Log::warning("Transaksi dengan Order ID {$orderId} tidak ditemukan.");
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    }
}