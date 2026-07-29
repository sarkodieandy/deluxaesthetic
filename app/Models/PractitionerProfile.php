<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'slug',
    'title',
    'professional_title',
    'biography',
    'qualifications',
    'certifications',
    'specialities',
    'years_experience',
    'photo_path',
    'is_ceo',
    'is_featured',
    'is_active',
    'sort_order',
    'social_links',
])]
class PractitionerProfile extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'specialities' => 'array',
            'qualifications' => 'array',
            'certifications' => 'array',
            'social_links' => 'array',
            'years_experience' => 'integer',
            'is_ceo' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PractitionerSchedule::class);
    }

    public function blockedDates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PractitionerBlockedDate::class);
    }

    public function photoUrl(): string
    {
        $path = $this->photo_path;

        if (! $path) {
            return asset(config('clinic.ceo.portrait_a'));
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset($path);
    }

    public function displayTitle(): string
    {
        return $this->professional_title ?: ($this->title ?: 'Practitioner');
    }

    public function social(string $network): ?string
    {
        $links = $this->social_links ?? [];

        return $links[$network] ?? null;
    }
}
