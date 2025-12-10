<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_method_id',
        'origin_city',
        'destination_city',
        'cost',
        'estimated_days',
        'min_weight',
        'max_weight',
        'is_active'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    // Get formatted cost
    public function getFormattedCostAttribute()
    {
        return 'Rp ' . number_format($this->cost, 0, ',', '.');
    }

    // Get estimated delivery text
    public function getEstimatedDeliveryTextAttribute()
    {
        if ($this->estimated_days === '60 menit' || $this->estimated_days === '1-2 jam') {
            return $this->estimated_days;
        }

        if (is_numeric($this->estimated_days)) {
            $days = $this->estimated_days;
            if ($days <= 1) {
                return '1 hari';
            } elseif ($days <= 7) {
                return "{$days} hari";
            } else {
                $weeks = floor($days / 7);
                return "{$weeks} minggu";
            }
        }

        return $this->estimated_days;
    }

    // Check if weight is within range
    public function isWeightInRange($weight)
    {
        return $weight >= $this->min_weight && $weight <= $this->max_weight;
    }

    // Check if this is fresh product friendly
    public function isFreshProductFriendly()
    {
        $method = $this->shippingMethod;
        return in_array($method->type, ['instant', 'same_day']);
    }

    // Get recommendation score for fresh products
    public function getFreshProductScoreAttribute()
    {
        $method = $this->shippingMethod;
        
        if ($method->type === 'instant') {
            return 100; // Perfect for fresh products
        } elseif ($method->type === 'same_day') {
            return 80; // Good for fresh products
        } elseif ($this->estimated_days === '1-2') {
            return 60; // Acceptable for fresh products
        } elseif ($this->estimated_days === '2-3') {
            return 40; // Risky for fresh products
        } else {
            return 20; // Not recommended for fresh products
        }
    }

    // Scope for active costs
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific route
    public function scopeForRoute($query, $origin, $destination)
    {
        return $query->where('origin_city', $origin)
                    ->where('destination_city', $destination);
    }

    // Scope for weight range
    public function scopeForWeight($query, $weight)
    {
        return $query->where('min_weight', '<=', $weight)
                    ->where('max_weight', '>=', $weight);
    }
}
