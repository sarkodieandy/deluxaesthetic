<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentTranslation extends Model
{
    protected $fillable = [
        'treatment_id', 'locale', 'name', 'short_description', 'description', 'benefits',
        'preparation_instructions', 'aftercare_instructions', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return ['benefits' => 'array'];
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}
