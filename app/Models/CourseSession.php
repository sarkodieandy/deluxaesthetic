<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSession extends Model
{
    protected $fillable = [
        'course_schedule_id',
        'session_date',
        'starts_at',
        'ends_at',
        'topic',
        'location',
        'trainer_profile_id',
        'status',
        'is_practical',
        'is_assessment',
        'announcement',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'is_practical' => 'boolean',
            'is_assessment' => 'boolean',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }
}
