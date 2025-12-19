<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'short_name',
        'code',
        'type',
        'manager_name',
        'phone',
        'email',
        'address',
        'province',
        'city',
        'postal_code',
        'loc_provinsi_id',
        'loc_kabkota_id',
        'loc_kecamatan_id',
        'loc_desa_id',
        'latitude',
        'longitude',
        'operating_hours',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'operating_hours' => 'array',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function inventories()
    {
        return $this->hasMany(OutletProductInventory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'outlet_product_inventories')
            ->withPivot(['stock', 'price_override', 'status'])
            ->withTimestamps();
    }

    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->province}, {$this->postal_code}";
    }

    public function getFormattedPhoneAttribute()
    {
        return $this->phone;
    }

    public function getLocProvinsiNameAttribute()
    {
        if (!$this->loc_provinsi_id) return null;
        return \DB::table('loc_provinsis')->where('id', $this->loc_provinsi_id)->value('name');
    }

    public function getLocKabkotaNameAttribute()
    {
        if (!$this->loc_kabkota_id) return null;
        return \DB::table('loc_kabkotas')->where('id', $this->loc_kabkota_id)->value('name');
    }

    public function getLocKecamatanNameAttribute()
    {
        if (!$this->loc_kecamatan_id) return null;
        return \DB::table('loc_kecamatans')->where('id', $this->loc_kecamatan_id)->value('name');
    }

    public function getLocDesaNameAttribute()
    {
        if (!$this->loc_desa_id) return null;
        return \DB::table('loc_desas')->where('id', $this->loc_desa_id)->value('name');
    }

    public function getLocationRefTextAttribute()
    {
        $parts = array_filter([
            $this->loc_desa_name,
            $this->loc_kecamatan_name,
            $this->loc_kabkota_name,
            $this->loc_provinsi_name,
        ]);
        return $parts ? implode(', ', $parts) : null;
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Aktif</span>'
            : '<span class="badge bg-danger">Tidak Aktif</span>';
    }

    public function getTypeBadgeAttribute()
    {
        $colors = [
            'main' => 'primary',
            'branch' => 'info',
            'pickup_point' => 'warning'
        ];
        
        $labels = [
            'main' => 'Utama',
            'branch' => 'Cabang',
            'pickup_point' => 'Pickup Point'
        ];
        
        $color = $colors[$this->type] ?? 'secondary';
        $label = $labels[$this->type] ?? ucfirst($this->type);
        
        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    public function getLocationUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://maps.google.com/?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }

    public function getOperatingHoursTextAttribute()
    {
        if (!$this->operating_hours || !is_array($this->operating_hours)) {
            return 'Tidak tersedia';
        }

        $text = [];
        foreach ($this->operating_hours as $day => $hours) {
            if ($hours['open'] && $hours['close']) {
                $text[] = ucfirst($day) . ": {$hours['open']} - {$hours['close']}";
            }
        }

        return !empty($text) ? implode('<br>', $text) : 'Tidak tersedia';
    }

    // Scope for active outlets
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope for search
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('short_name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('manager_name', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    }

    // Scope by city
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }
}
