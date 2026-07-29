<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'slug',
    'title',
    'headline',
    'bio',
    'qualifications',
    'specialities',
    'years_experience',
    'portrait_path',
    'is_featured',
    'is_active',
    'sort_order',
])]
class TrainerProfile extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'qualifications' => 'array',
            'specialities' => 'array',
            'years_experience' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
