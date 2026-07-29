<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_category_id', 'trainer_profile_id', 'name', 'slug', 'description', 'learning_outcomes',
        'entry_requirements', 'delivery_mode', 'duration_hours', 'venue', 'max_students', 'waiting_list_capacity',
        'fee', 'deposit_amount', 'instalment_rules', 'included_materials', 'required_equipment', 'assessment_rules',
        'attendance_rules', 'certificate_rules', 'image_path', 'video_url', 'is_featured', 'is_active', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'learning_outcomes' => 'array',
            'included_materials' => 'array',
            'instalment_rules' => 'array',
            'assessment_rules' => 'array',
            'attendance_rules' => 'array',
            'certificate_rules' => 'array',
            'fee' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(TrainerProfile::class, 'trainer_profile_id');
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return str_starts_with($this->image_path, 'assets/') ? asset($this->image_path) : asset('storage/'.$this->image_path);
    }
}
