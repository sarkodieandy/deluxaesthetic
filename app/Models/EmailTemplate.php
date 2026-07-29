<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key',
        'event',
        'channel',
        'locale',
        'name',
        'subject',
        'preheader',
        'body_html',
        'body_text',
        'available_variables',
        'active',
        'system_template',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'available_variables' => 'array',
            'active' => 'boolean',
            'system_template' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
