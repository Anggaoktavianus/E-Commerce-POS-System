<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image',
        'attachments',
        'video_url',
        'meta_title',
        'meta_description',
        'is_published',
        'created_by'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_published' => 'boolean'
    ];

    /**
     * Get the user that created the page.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate a URL-friendly slug from the page title.
     */
    public static function generateSlug($title)
    {
        $slug = Str::slug($title);
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }
    
    /**
     * Set meta tags for the page.
     */
    public function setMeta()
    {
        $defaultTitle = config('app.name') . ' - ' . $this->title;
        $defaultDescription = $this->meta_description ?: 
            Str::limit(strip_tags($this->content), 160);
        
        // Set default meta if not set
        $this->meta_title = $this->meta_title ?: $defaultTitle;
        $this->meta_description = $this->meta_description ?: $defaultDescription;
        
        // Share with all views
        view()->share([
            'metaTitle' => $this->meta_title,
            'metaDescription' => $this->meta_description,
            'metaImage' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'metaUrl' => url()->current(),
        ]);
        
        return $this;
    }
    
    /**
     * Get the URL to the page.
     */
    public function getUrlAttribute()
    {
        return route('pages.show', $this->slug);
    }
}
