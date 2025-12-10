<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'transaction_id', 'order_id_midtrans', 'payment_type',
        'payment_method', 'status', 'gross_amount', 'currency',
        'transaction_details', 'va_numbers', 'bill_key', 'biller_code'
    ];

    protected $casts = [
        'transaction_details' => 'array',
        'va_numbers' => 'array',
        'bill_key' => 'array',
        'biller_code' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedAmountAttribute()
    {
        return 'IDR ' . number_format($this->gross_amount, 0, ',', '.');
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'capture' => 'info',
            'settlement' => 'success',
            'deny' => 'danger',
            'cancel' => 'secondary',
            'expire' => 'dark',
            'refund' => 'warning'
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status);
    }
}
