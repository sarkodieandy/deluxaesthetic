<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'student_number',
    'portrait_path',
    'phone',
    'address_line_1',
    'address_line_2',
    'city',
    'region',
    'country',
    'education_level',
    'emergency_contact_name',
    'emergency_contact_phone',
    'notification_preferences',
    'profile_completed_at',
    'portal_invited_at',
    'portal_activated_at',
    'invitation_token',
    'invitation_expires_at',
    'bio',
    'documents',
])]
class StudentProfile extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'documents' => 'array',
            'notification_preferences' => 'array',
            'profile_completed_at' => 'datetime',
            'portal_invited_at' => 'datetime',
            'portal_activated_at' => 'datetime',
            'invitation_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
