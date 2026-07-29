<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstalmentPlan extends Model
{
    protected $fillable = ['enrolment_id', 'sequence', 'amount', 'due_on', 'status', 'paid_at'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_on' => 'date',
            'paid_at' => 'datetime',
        ];
    }
}
