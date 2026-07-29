<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'type', 'description', 'image_path', 'before_image_path', 'after_image_path',
        'alt_text', 'is_featured', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function imageUrl(): ?string
    {
        return $this->publicAssetUrl($this->image_path);
    }

    public function beforeImageUrl(): ?string
    {
        return $this->publicAssetUrl($this->before_image_path);
    }

    public function afterImageUrl(): ?string
    {
        return $this->publicAssetUrl($this->after_image_path);
    }

    public function hasBeforeAfterPair(): bool
    {
        return $this->type === 'before_after'
            && $this->publicAssetUrl($this->before_image_path) !== null
            && $this->publicAssetUrl($this->after_image_path) !== null;
    }

    private function publicAssetUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (\App\Support\GalleryMedia::isRemoteUrl($path)) {
            return $path;
        }

        if (str_starts_with($path, 'assets/')) {
            $full = public_path($path);

            return is_file($full) ? asset($path) : null;
        }

        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return '/storage/'.str_replace('\\', '/', ltrim($path, '/'));
    }
}
