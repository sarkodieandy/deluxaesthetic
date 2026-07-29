<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'slug',
    'email',
    'phone',
    'whatsapp',
    'address_line_1',
    'address_line_2',
    'city',
    'region',
    'country',
    'postal_code',
    'latitude',
    'longitude',
    'opening_hours',
    'map_embed_url',
    'hours_summary',
    'is_active',
    'is_primary',
    'sort_order',
])]
class Branch extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function practitionerProfiles(): HasMany
    {
        return $this->hasMany(PractitionerProfile::class);
    }
}
