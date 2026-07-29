<?php

namespace App\Events;

use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentAccountActivated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public Enrolment $enrolment,
        public User $activatedBy,
    ) {}
}
