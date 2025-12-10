<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Outlet;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'domain',
        'owner_name',
        'email',
        'phone',
        'address',
        'province',
        'city',
        'postal_code',
        'loc_provinsi_id',
        'loc_kabkota_id',
        'loc_kecamatan_id',
        'loc_desa_id',
        'tax_id',
        'business_license',
        'logo_url',
        'theme',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }

    public function activeOutlets()
    {
        return $this->outlets()->where('is_active', true);
    }

    public function mainOutlet()
    {
        return $this->outlets()->where('type', 'main')->first();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
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

    public function getOutletCountAttribute()
    {
        return $this->outlets()->count();
    }

    public function getActiveOutletCountAttribute()
    {
        return $this->activeOutlets()->count();
    }

    // Scope for active stores
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for search
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('owner_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }
}
