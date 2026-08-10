<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Treatment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'treatment_category_id', 'name', 'slug', 'short_description', 'description', 'benefits',
        'suitable_candidates', 'contraindications', 'preparation_instructions', 'aftercare_instructions',
        'duration_minutes', 'recovery_days', 'price', 'promotional_price', 'deposit_amount',
        'recommended_sessions', 'buffer_before_minutes', 'buffer_after_minutes', 'is_featured', 'sort_order',
        'is_active', 'seo_title', 'seo_description', 'image_path',
    ];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'price' => 'decimal:2',
            'promotional_price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TreatmentCategory::class, 'treatment_category_id');
    }

    public function practitioners(): BelongsToMany
    {
        return $this->belongsToMany(PractitionerProfile::class, 'treatment_practitioner');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TreatmentTranslation::class);
    }

    public function galleryItems(): HasMany
    {
        return $this->hasMany(GalleryItem::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function effectivePrice(): string
    {
        return (string) ($this->promotional_price ?? $this->price);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (\App\Support\GalleryMedia::isRemoteUrl($this->image_path)) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'assets/')) {
            return is_file(public_path($this->image_path)) ? asset($this->image_path) : null;
        }

        return Storage::disk('public')->exists($this->image_path)
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }
}
