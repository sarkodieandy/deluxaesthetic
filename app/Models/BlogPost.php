<?php

namespace App\Models;

use App\Support\GalleryMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id', 'title', 'slug', 'category', 'excerpt', 'body', 'cover_image_path',
        'cover_image_alt', 'status', 'is_featured', 'published_at', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'published_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublic(): bool
    {
        return $this->status === 'published' && $this->published_at?->isPast();
    }

    public function coverImageUrl(): ?string
    {
        if (! $this->cover_image_path) {
            return null;
        }

        if (GalleryMedia::isRemoteUrl($this->cover_image_path)) {
            return $this->cover_image_path;
        }

        return Storage::disk('public')->exists($this->cover_image_path)
            ? '/storage/'.ltrim($this->cover_image_path, '/')
            : null;
    }
}
