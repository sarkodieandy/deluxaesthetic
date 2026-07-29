<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = ['course_id', 'title', 'max_score', 'passing_score'];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'passing_score' => 'decimal:2',
        ];
    }
}
