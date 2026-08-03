<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    protected $fillable = ['course_id', 'title', 'instructions', 'due_at', 'attachment_path', 'allow_resubmission'];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'allow_resubmission' => 'boolean'];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
