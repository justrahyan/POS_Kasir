<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $settings = Setting::pluck('value', 'key')->all();
        $categories = Category::all();
        return view('kasir.index', compact('products', 'settings', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'total' => 'required|numeric',
            'bayar' => 'required|numeric',
            'kembalian' => 'required|numeric',
            'metode_pembayaran' => 'required|string',
            'items' => 'required|array|min:1',
            'invoice_number' => 'nullable|string',
            'status' => 'nullable|string',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.subtotal' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'total' => $validated['total'],
                'bayar' => $validated['bayar'],
                'kembalian' => $validated['kembalian'],
                'invoice_number' => $validated['invoice_number'] ?? 'INV-' . time(),
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status' => $validated['status'] ?? 'paid',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['id']);
                
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'product_name' => $product->name,
                    'product_image' => $product->image,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'transaction_id' => $transaction->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal saat menyimpan transaksi.',
            ], 500);
        }
    }

    public function laporan(Request $request)
    {
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');

        $allowedColumns = ['id', 'created_at', 'total'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }

        $metode = $request->input('metode');

        $query = Transaction::with('details');

        if ($metode && in_array($metode, ['cash', 'qris'])) {
            $query->where('metode_pembayaran', $metode);
        }

        $query->orderBy($sortColumn, $sortDirection);

        $transactions = $query->paginate(20)->withQueryString();

        return view('laporan.index', compact('transactions'));
    }
}