<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'guest_name',
    'guest_email',
    'guest_phone',
    'date_of_birth',
    'gender',
    'address_line_1',
    'address_line_2',
    'city',
    'region',
    'country',
    'postal_code',
    'emergency_contact_name',
    'emergency_contact_phone',
    'preferred_branch_id',
    'preferences',
    'notes',
    'marketing_opt_in',
    'loyalty_points',
    'referral_code',
])]
class ClientProfile extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'preferences' => 'array',
            'marketing_opt_in' => 'boolean',
            'loyalty_points' => 'integer',
        ];
    }

    public function displayName(): string
    {
        return $this->user?->name
            ?? $this->guest_name
            ?? 'Guest client';
    }

    public function displayEmail(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    public function displayPhone(): ?string
    {
        return $this->user?->phone ?? $this->guest_phone;
    }

    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function preferredBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'preferred_branch_id');
    }
}
