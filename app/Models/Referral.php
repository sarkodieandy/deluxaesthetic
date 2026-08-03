<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = ['referrer_client_profile_id', 'referred_name', 'referred_email', 'referred_phone', 'status', 'reward_points', 'notes', 'converted_at', 'created_by'];

    protected function casts(): array
    {
        return ['converted_at' => 'datetime'];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class, 'referrer_client_profile_id');
    }
}
