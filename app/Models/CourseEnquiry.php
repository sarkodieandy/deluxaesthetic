<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseEnquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'user_id',
        'full_name',
        'email',
        'phone',
        'preferred_training_date',
        'professional_background',
        'preferred_contact_method',
        'message',
        'privacy_consent',
        'status',
        'assigned_to',
        'converted_student_profile_id',
        'converted_enrolment_id',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'preferred_training_date' => 'date',
            'privacy_consent' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
