<?php

namespace App\Models;

use App\Support\GalleryMedia;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'context', 'quote', 'rating', 'image_path', 'is_featured', 'is_active', 'sort_order', 'created_by'];

    protected function casts(): array
    {
        return ['rating' => 'integer', 'is_featured' => 'boolean', 'is_active' => 'boolean'];
    }

    public function imageUrl(): ?string
    {
        return ! $this->image_path ? null : (GalleryMedia::isRemoteUrl($this->image_path) ? $this->image_path : asset('storage/'.$this->image_path));
    }
}
