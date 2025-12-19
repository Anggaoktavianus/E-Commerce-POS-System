<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'outlet_id',
        'type',
        'quantity',
        'old_stock',
        'new_stock',
        'reference_type',
        'reference_id',
        'reference_number',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'old_stock' => 'integer',
        'new_stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'in' => 'Stock Masuk',
            'out' => 'Stock Keluar',
            'adjustment' => 'Penyesuaian Manual',
            'restore' => 'Restore Stock',
            default => $this->type
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'in' => 'success',
            'out' => 'danger',
            'adjustment' => 'info',
            'restore' => 'warning',
            default => 'secondary'
        };
    }
}
