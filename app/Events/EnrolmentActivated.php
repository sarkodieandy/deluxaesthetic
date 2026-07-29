<?php

namespace App\Events;

use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrolmentActivated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Enrolment $enrolment,
        public User $activatedBy,
    ) {}
}
