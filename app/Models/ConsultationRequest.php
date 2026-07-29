<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'treatment_id',
        'name',
        'email',
        'phone',
        'preferred_date',
        'preferred_channel',
        'description',
        'attachments',
        'consent_accepted',
        'assigned_to',
        'internal_notes',
        'client_response',
        'status',
        'follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'attachments' => 'array',
            'consent_accepted' => 'boolean',
            'follow_up_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
