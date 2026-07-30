<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function forRoute(?string $routeName): ?self
    {
        if (! $routeName) {
            return null;
        }

        return static::where('route_name', $routeName)->first();
    }
}
