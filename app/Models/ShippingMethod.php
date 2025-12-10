<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code', 
        'type',
        'logo_url',
        'is_active',
        'service_areas',
        'max_distance_km'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'service_areas' => 'array',
        'max_distance_km' => 'integer'
    ];

    public function costs()
    {
        return $this->hasMany(ShippingCost::class);
    }

    public function shippingCosts()
    {
        return $this->hasMany(ShippingCost::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Auto-calculate cost for specific route
    public function calculateCost($origin, $destination, $weight = 1)
    {
        return $this->costs()
            ->where('origin_city', $origin)
            ->where('destination_city', $destination)
            ->where('min_weight', '<=', $weight)
            ->where('max_weight', '>=', $weight)
            ->where('is_active', true)
            ->first();
    }

    // Check if method is available for route
    public function isAvailable($origin, $destination)
    {
        // Check service areas
        if ($this->service_areas && !empty($this->service_areas)) {
            if (!in_array($destination, $this->service_areas)) {
                return false;
            }
        }

        // Check if we have cost data for this route
        return $this->costs()
            ->where('origin_city', $origin)
            ->where('destination_city', $destination)
            ->where('is_active', true)
            ->exists();
    }

    // Get formatted type for display
    public function getFormattedTypeAttribute()
    {
        $types = [
            'instant' => 'Instan',
            'same_day' => 'Same Day',
            'regular' => 'Reguler',
            'express' => 'Express',
            'pickup' => 'Ambil Sendiri'
        ];

        return $types[$this->type] ?? 'Reguler';
    }

    // Get badge color based on type
    public function getTypeBadgeColorAttribute()
    {
        $colors = [
            'instant' => 'success',
            'same_day' => 'info', 
            'regular' => 'secondary',
            'express' => 'primary',
            'pickup' => 'warning'
        ];

        return $colors[$this->type] ?? 'secondary';
    }

    // Scope for active methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for instant delivery methods
    public function scopeInstant($query)
    {
        return $query->where('type', 'instant');
    }

    // Scope for same city methods
    public function scopeSameCity($query)
    {
        return $query->whereIn('type', ['instant', 'same_day']);
    }
}
