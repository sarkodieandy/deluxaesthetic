<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrolment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'student_profile_id',
        'course_id',
        'course_schedule_id',
        'branch_id',
        'trainer_profile_id',
        'status',
        'fee',
        'discount',
        'deposit_required',
        'amount_paid',
        'outstanding_balance',
        'currency',
        'documents',
        'policies_accepted',
        'enrolment_date',
        'physical_verification_date',
        'verified_by',
        'confirmed_at',
        'activated_at',
        'activated_by',
        'completed_at',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'deposit_required' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
            'documents' => 'array',
            'policies_accepted' => 'boolean',
            'confirmed_at' => 'datetime',
            'activated_at' => 'datetime',
            'completed_at' => 'datetime',
            'enrolment_date' => 'date',
            'physical_verification_date' => 'date',
        ];
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function hasIssuedCertificate(): bool
    {
        return $this->certificates()->where('status', 'issued')->exists();
    }
}
