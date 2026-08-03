<?php

namespace App\Services\Academy;

use App\Models\CourseEnquiry;
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
     * @param  array{name: string, email: string, phone?: string|null, course_id?: int|null, professional_background?: string|null, message: string, password: string}  $data
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
                'is_active' => false,
                'profile_completed_at' => now(),
                'portal_activated_at' => null,
                'accepted_privacy_at' => now(),
            ]);

            $user->assignRole(Role::findOrCreate('Student'));

            StudentProfile::create([
                'user_id' => $user->id,
                'student_number' => $this->enrolments->allocateStudentNumber(),
                'phone' => $data['phone'] ?? null,
                'profile_completed_at' => now(),
            ]);

            CourseEnquiry::create([
                'course_id' => $data['course_id'] ?? null,
                'user_id' => $user->id,
                'full_name' => $user->name,
                'email' => $user->email,
                'phone' => $data['phone'] ?? '',
                'professional_background' => $data['professional_background'] ?? null,
                'preferred_contact_method' => 'whatsapp',
                'message' => $data['message'],
                'privacy_consent' => true,
                'status' => 'submitted',
            ]);

            return $user->fresh(['studentProfile']);
        });
    }
}
