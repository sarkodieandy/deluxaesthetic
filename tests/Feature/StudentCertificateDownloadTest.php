<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSchedule;
use App\Models\Enrolment;
use App\Models\User;
use App\Services\Academy\CertificateIssuanceService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentCertificateDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_student_can_download_own_issued_certificate(): void
    {
        [$student, $certificate] = $this->seedStudentWithCertificate();

        $this->actingAs($student)
            ->get(route('student.certificates.download', $certificate))
            ->assertOk()
            ->assertHeader('content-disposition');

        Storage::disk('public')->assertExists($certificate->fresh()->pdf_path);
    }

    public function test_student_cannot_download_another_students_certificate(): void
    {
        [, $certificate] = $this->seedStudentWithCertificate();

        $intruder = User::factory()->create(['email_verified_at' => now(), 'is_active' => true]);
        $intruder->assignRole(Role::findOrCreate('Student'));
        \App\Models\StudentProfile::query()->create([
            'user_id' => $intruder->id,
            'student_number' => 'STU-2026-0199',
            'profile_completed_at' => now(),
        ]);

        $this->actingAs($intruder)
            ->get(route('student.certificates.download', $certificate))
            ->assertForbidden();
    }

    public function test_student_certificates_index_lists_download_link(): void
    {
        [$student, $certificate] = $this->seedStudentWithCertificate();

        $this->actingAs($student)
            ->get(route('student.certificates.index'))
            ->assertOk()
            ->assertSee($certificate->number)
            ->assertSee(__('student.certificates.download'));
    }

    /**
     * @return array{0: User, 1: \App\Models\Certificate}
     */
    private function seedStudentWithCertificate(): array
    {
        $student = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole(Role::findOrCreate('Student'));
        $profile = \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-2026-0100',
            'profile_completed_at' => now(),
        ]);

        $category = CourseCategory::query()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $course = Course::query()->create([
            'course_category_id' => $category->id,
            'name' => 'Demo Course',
            'slug' => 'demo-course',
            'fee' => 1000,
            'is_active' => true,
        ]);

        $schedule = CourseSchedule::query()->create([
            'course_id' => $course->id,
            'starts_on' => now()->subMonth()->toDateString(),
            'ends_on' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '16:00:00',
            'capacity' => 10,
            'is_active' => true,
        ]);

        $enrolment = Enrolment::query()->create([
            'reference' => 'ENR-TEST-001',
            'student_profile_id' => $profile->id,
            'course_id' => $course->id,
            'course_schedule_id' => $schedule->id,
            'status' => 'completed',
            'fee' => 1000,
            'amount_paid' => 1000,
            'outstanding_balance' => 0,
            'currency' => 'GHS',
            'policies_accepted' => true,
            'confirmed_at' => now()->subMonth(),
        ]);

        $certificate = app(CertificateIssuanceService::class)->createForEnrolment($enrolment, [
            'completion_date' => now()->toDateString(),
            'issue' => true,
        ]);

        return [$student, $certificate];
    }
}
