<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'enrolment_id', 'course_schedule_id', 'session_date', 'status', 'notes', 'recorded_by',
    ];

    protected function casts(): array
    {
        return ['session_date' => 'date'];
    }
}
