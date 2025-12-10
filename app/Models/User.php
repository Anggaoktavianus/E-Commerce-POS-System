<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'loc_provinsi_id',
        'loc_kabkota_id',
        'loc_kecamatan_id',
        'loc_desa_id',
        'role',
        'company_name',
        'company_address',
        'company_phone',
        'npwp',
        'is_verified',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
    ];

    // Role check methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMitra(): bool
    {
        return $this->role === 'mitra';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer' || $this->role === null;
    }

    // Scope for each role
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeMitras($query)
    {
        return $query->where('role', 'mitra');
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer')->orWhereNull('role');
    }

    // Check if user has verified their email
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    // Check if user is verified (for mitra)
    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    // Get the user's full address
    public function getFullAddressAttribute(): string
    {
        return $this->address . 
               ($this->company_address ? "\n\nAlamat Perusahaan:\n" . $this->company_address : '');
    }

    // Get the user's contact information
    public function getContactInfoAttribute(): string
    {
        $info = "Email: {$this->email}";
        if ($this->phone) {
            $info .= "\nTelepon: {$this->phone}";
        }
        if ($this->company_phone) {
            $info .= "\nTelepon Perusahaan: {$this->company_phone}";
        }
        return $info;
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
}