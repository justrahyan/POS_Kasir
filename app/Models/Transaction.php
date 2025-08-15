<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'status',
        'payment_token',
        'invoice_number',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
