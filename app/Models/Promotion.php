<?php

namespace App\Models;

use App\Support\GalleryMedia;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = ['title', 'subtitle', 'image_path', 'mobile_image_path', 'placement', 'cta_label', 'cta_url', 'coupon_code', 'background_color', 'text_color', 'priority', 'starts_at', 'ends_at', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];
    }

    public function scopeLive($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function imageUrl(): ?string
    {
        return ! $this->image_path ? null : (GalleryMedia::isRemoteUrl($this->image_path) ? $this->image_path : asset('storage/'.$this->image_path));
    }

    public function mobileImageUrl(): ?string
    {
        return ! $this->mobile_image_path ? null : (GalleryMedia::isRemoteUrl($this->mobile_image_path) ? $this->mobile_image_path : asset('storage/'.$this->mobile_image_path));
    }
}
