<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KategoriArtikel extends Model
{
    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'gambar',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function artikels(): HasMany
    {
        return $this->hasMany(Artikel::class);
    }

    public function getArtikelCountAttribute(): int
    {
        return $this->artikels()->where('status', 'published')->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });

        static::updating(function ($kategori) {
            if ($kategori->isDirty('nama') && empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });
    }
}
