<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletProductInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'product_id',
        'stock',
        'price_override',
        'status',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
