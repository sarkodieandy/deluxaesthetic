<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialDownload extends Model
{
    protected $fillable = [
        'course_material_id', 'student_profile_id', 'enrolment_id', 'ip_address', 'downloaded_at',
    ];

    protected function casts(): array
    {
        return ['downloaded_at' => 'datetime'];
    }
}
