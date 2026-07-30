<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class WebPage extends Model
{
    protected $fillable = [
        'name', 'slug', 'route_name', 'seo_title', 'meta_description',
        'hero_eyebrow', 'hero_title', 'hero_body', 'hero_image_url',
        'sections', 'is_published', 'published_at', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (self $page) => Cache::forget('web-page.'.$page->route_name));
        static::deleted(fn (self $page) => Cache::forget('web-page.'.$page->route_name));
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function forRoute(?string $routeName): ?self
    {
        if (! $routeName) {
            return null;
        }

        return Cache::remember('web-page.'.$routeName, now()->addHour(), fn () => static::where('route_name', $routeName)->first());
    }
}
