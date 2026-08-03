<?php

namespace App\Services\Academy;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StudentPortalRegistrationService
{
    public function __construct(
        private readonly PhysicalEnrolmentService $enrolments,
    ) {}

    /**
     * @param  array{name: string, email: string, phone?: string|null, password: string}  $data
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
                'locale' => app()->getLocale(),
                'is_active' => true,
                'profile_completed_at' => now(),
                'accepted_privacy_at' => now(),
            ]);

            $user->assignRole(Role::findOrCreate('Student'));

            StudentProfile::create([
                'user_id' => $user->id,
                'student_number' => $this->enrolments->allocateStudentNumber(),
                'phone' => $data['phone'] ?? null,
                'profile_completed_at' => now(),
            ]);

            return $user->fresh(['studentProfile']);
        });
    }
}
