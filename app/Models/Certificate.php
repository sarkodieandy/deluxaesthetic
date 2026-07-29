<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'enrolment_id',
        'student_profile_id',
        'course_id',
        'trainer_profile_id',
        'student_name',
        'course_name',
        'completion_date',
        'signatory',
        'verification_code',
        'qr_path',
        'pdf_path',
        'status',
        'issued_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'completion_date' => 'date',
            'issued_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(Enrolment::class);
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    public function isDownloadable(): bool
    {
        return $this->isIssued()
            && $this->pdf_path
            && Storage::disk('public')->exists($this->pdf_path);
    }

    public function downloadFilename(): string
    {
        return 'certificate-'.str_replace('/', '-', $this->number).'.pdf';
    }
}
