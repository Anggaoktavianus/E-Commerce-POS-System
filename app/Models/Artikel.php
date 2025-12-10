<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Artikel extends Model
{
    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'gambar_utama',
        'gambar_thumbnail',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'kategori_artikel_id',
        'user_id',
        'views',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'status' => 'string',
    ];

    public function kategoriArtikel(): BelongsTo
    {
        return $this->belongsTo(KategoriArtikel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getExcerptAttribute(int $length = 150): string
    {
        return Str::limit(strip_tags($this->konten), $length);
    }

    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->konten));
        return max(1, ceil($wordCount / 200));
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artikel) {
            if (empty($artikel->slug)) {
                $artikel->slug = Str::slug($artikel->judul);
            }
            
            if ($artikel->status === 'published' && !$artikel->published_at) {
                $artikel->published_at = now();
            }
        });

        static::updating(function ($artikel) {
            if ($artikel->isDirty('judul') && empty($artikel->slug)) {
                $artikel->slug = Str::slug($artikel->judul);
            }
            
            if ($artikel->isDirty('status') && $artikel->status === 'published' && !$artikel->published_at) {
                $artikel->published_at = now();
            }
        });
    }
}
