<?php

namespace App\Models;

use App\Support\GalleryMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    use SoftDeletes;

    public const DEFAULT_LOCATION_GROUP = 'global';

    /** @var array<string, string> */
    public const LOCATION_GROUPS = [
        'global' => 'Global Gallery',
        'ghana' => 'Ghana',
        'cameroon' => 'Cameroon',
        'cote-divoire' => 'Côte d’Ivoire',
        'senegal' => 'Senegal',
        'benin-republic' => 'Benin Republic',
    ];

    /** @var array<string, string> */
    public const LOCATION_GROUP_DESCRIPTIONS = [
        'global' => 'Brand stories and photographs that are not tied to one destination.',
        'ghana' => 'Accra clinic moments, local academy sessions and Ghana events.',
        'cameroon' => 'Training visits, student moments and clinical work from Cameroon.',
        'cote-divoire' => 'Academy and professional highlights from Côte d’Ivoire.',
        'senegal' => 'Training, events and student stories captured in Senegal.',
        'benin-republic' => 'Academy visits and professional experiences from Benin Republic.',
    ];

    protected $fillable = [
        'treatment_id', 'title', 'slug', 'type', 'location_group', 'description', 'image_path', 'before_image_path', 'after_image_path',
        'alt_text', 'is_featured', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function locationGroupLabel(): string
    {
        return self::LOCATION_GROUPS[$this->location_group ?? self::DEFAULT_LOCATION_GROUP]
            ?? self::LOCATION_GROUPS[self::DEFAULT_LOCATION_GROUP];
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

        if (GalleryMedia::isRemoteUrl($path)) {
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
