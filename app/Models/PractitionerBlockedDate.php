<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PractitionerBlockedDate extends Model
{
    protected $fillable = [
        'practitioner_profile_id',
        'starts_on',
        'ends_on',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function practitioner(): BelongsTo
    {
        return $this->belongsTo(PractitionerProfile::class, 'practitioner_profile_id');
    }

    public function coversDate(\DateTimeInterface|string $date): bool
    {
        $day = \Carbon\CarbonImmutable::parse($date)->toDateString();

        return $day >= $this->starts_on->toDateString()
            && $day <= $this->ends_on->toDateString();
    }
}
