<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'recipient_phone',
        'address',
        'province',
        'city',
        'postal_code',
        'country',
        'loc_provinsi_id',
        'loc_kabkota_id',
        'loc_kecamatan_id',
        'loc_desa_id',
        'latitude',
        'longitude',
        'notes',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->notes,
            $this->loc_desa_name,
            $this->loc_kecamatan_name,
            $this->loc_kabkota_name,
            $this->loc_provinsi_name,
            $this->postal_code,
        ]);
        
        return implode(', ', $parts);
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // Methods
    public function setAsPrimary()
    {
        // Unset other primary addresses for this user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
        
        $this->update(['is_primary' => true]);
    }
}
