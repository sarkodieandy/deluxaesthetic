<?php

namespace App\Policies\Student;

use App\Models\Enrolment;
use App\Models\User;

class EnrolmentPolicy
{
    public function view(User $user, Enrolment $enrolment): bool
    {
        return (int) $user->studentProfile?->id === (int) $enrolment->student_profile_id;
    }
}
