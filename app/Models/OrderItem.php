<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'price', 
        'quantity', 'total', 'product_details'
    ];

    protected $casts = [
        'product_details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'IDR ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedTotalAttribute()
    {
        return 'IDR ' . number_format($this->total, 0, ',', '.');
    }
}
