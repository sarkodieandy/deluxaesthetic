<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseMaterial;
use App\Models\CourseSchedule;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\StudentSupportRequest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AcademyPrivateFileSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Storage::fake('academy_private');
        Storage::fake('public');
    }

    public function test_assignment_files_are_private_and_isolated_to_the_selected_student(): void
    {
        [$course, $schedule] = $this->courseAndSchedule();
        [$student, $enrolment] = $this->activeStudent($course, $schedule, 'STU-PRIVATE-1');
        [$otherStudent] = $this->activeStudent($course, $schedule, 'STU-PRIVATE-2');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.assignments.store'), [
            'course_id' => $course->id,
            'enrolment_id' => $enrolment->id,
            'title' => 'Private clinical worksheet',
            'instructions' => 'Complete this practical assessment.',
            'attachment' => UploadedFile::fake()->create('worksheet.pdf', 100, 'application/pdf'),
            'allow_resubmission' => '1',
        ])->assertRedirect();

        $assignment = Assignment::query()->firstOrFail();
        Storage::disk('academy_private')->assertExists($assignment->attachment_path);
        Storage::disk('public')->assertMissing($assignment->attachment_path);

        $this->actingAs($student)
            ->get(route('student.assignments.download', $assignment))
            ->assertOk();

        $this->actingAs($otherStudent)
            ->get(route('student.assignments.download', $assignment))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.assignments.download', $assignment))
            ->assertOk();

        $this->actingAs($student)->post(route('student.assignments.submit', $assignment), [
            'file' => UploadedFile::fake()->create('completed.pdf', 80, 'application/pdf'),
            'notes' => 'Completed work',
        ])->assertRedirect();

        $submission = AssignmentSubmission::query()->firstOrFail();
        Storage::disk('academy_private')->assertExists($submission->file_path);
        Storage::disk('public')->assertMissing($submission->file_path);

        $this->actingAs($admin)
            ->get(route('admin.assignment-submissions.download', $submission))
            ->assertOk();
    }

    public function test_secure_files_command_moves_legacy_public_academy_files_idempotently(): void
    {
        [$course, $schedule] = $this->courseAndSchedule();
        [, $enrolment] = $this->activeStudent($course, $schedule, 'STU-MIGRATE-1');

        $material = CourseMaterial::query()->create([
            'course_id' => $course->id,
            'enrolment_id' => $enrolment->id,
            'title' => 'Legacy material',
            'type' => 'document',
            'file_path' => 'course-materials/legacy.pdf',
            'is_published' => true,
        ]);
        $assignment = Assignment::query()->create([
            'course_id' => $course->id,
            'enrolment_id' => $enrolment->id,
            'title' => 'Legacy assignment',
            'attachment_path' => 'assignments/resources/legacy.pdf',
        ]);
        $submission = AssignmentSubmission::query()->create([
            'assignment_id' => $assignment->id,
            'enrolment_id' => $enrolment->id,
            'file_path' => 'assignments/submissions/legacy.pdf',
            'submitted_at' => now(),
        ]);
        $certificate = Certificate::query()->create([
            'number' => 'DLX-2026-PRIVATE-1',
            'enrolment_id' => $enrolment->id,
            'student_profile_id' => $enrolment->student_profile_id,
            'course_id' => $course->id,
            'student_name' => 'Secure Student',
            'course_name' => $course->name,
            'completion_date' => now()->toDateString(),
            'verification_code' => 'PRIVATEFILE1',
            'pdf_path' => 'certificates/legacy.pdf',
            'qr_path' => 'certificates/legacy-qr.png',
            'status' => 'issued',
            'issued_at' => now(),
        ]);

        $paths = [
            $material->file_path,
            $assignment->attachment_path,
            $submission->file_path,
            $certificate->pdf_path,
            $certificate->qr_path,
        ];

        foreach ($paths as $path) {
            Storage::disk('public')->put($path, 'legacy private content');
        }

        $this->artisan('academy:secure-files')->assertSuccessful();

        foreach ($paths as $path) {
            Storage::disk('academy_private')->assertExists($path);
            Storage::disk('public')->assertMissing($path);
        }

        $this->artisan('academy:secure-files')->assertSuccessful();

        foreach ($paths as $path) {
            Storage::disk('academy_private')->assertExists($path);
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_student_without_profile_is_denied_certificate_and_support_indexes(): void
    {
        [$course, $schedule] = $this->courseAndSchedule();
        [, $ownerEnrolment] = $this->activeStudent($course, $schedule, 'STU-OWNER-1');

        Certificate::query()->create([
            'number' => 'DLX-2026-OWNER-1',
            'enrolment_id' => $ownerEnrolment->id,
            'student_profile_id' => $ownerEnrolment->student_profile_id,
            'course_id' => $course->id,
            'student_name' => 'Owner Student',
            'course_name' => $course->name,
            'completion_date' => now()->toDateString(),
            'verification_code' => 'OWNERONLY01',
            'status' => 'issued',
            'issued_at' => now(),
        ]);
        StudentSupportRequest::query()->create([
            'reference' => 'SUP-OWNER-1',
            'student_profile_id' => $ownerEnrolment->student_profile_id,
            'enrolment_id' => $ownerEnrolment->id,
            'category' => 'course_question',
            'subject' => 'Confidential support subject',
            'message' => 'Private student support message',
            'status' => 'open',
        ]);

        $profilelessStudent = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $profilelessStudent->assignRole(Role::findOrCreate('Student'));

        $this->actingAs($profilelessStudent)
            ->get(route('student.certificates.index'))
            ->assertForbidden();

        $this->actingAs($profilelessStudent)
            ->get(route('student.support.index'))
            ->assertForbidden();
    }

    /**
     * @return array{0: Course, 1: CourseSchedule}
     */
    private function courseAndSchedule(): array
    {
        $category = CourseCategory::query()->create([
            'name' => 'Private Academy',
            'slug' => 'private-academy-'.str()->lower(str()->random(6)),
            'is_active' => true,
        ]);
        $course = Course::query()->create([
            'course_category_id' => $category->id,
            'name' => 'Private Academy Course',
            'slug' => 'private-academy-course-'.str()->lower(str()->random(6)),
            'fee' => 1200,
            'is_active' => true,
        ]);
        $schedule = CourseSchedule::query()->create([
            'course_id' => $course->id,
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addMonth()->toDateString(),
            'capacity' => 10,
            'is_active' => true,
        ]);

        return [$course, $schedule];
    }

    /**
     * @return array{0: User, 1: Enrolment}
     */
    private function activeStudent(Course $course, CourseSchedule $schedule, string $studentNumber): array
    {
        $student = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole(Role::findOrCreate('Student'));
        $profile = StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => $studentNumber,
            'profile_completed_at' => now(),
        ]);
        $enrolment = Enrolment::query()->create([
            'reference' => 'ENR-'.$studentNumber,
            'student_profile_id' => $profile->id,
            'course_id' => $course->id,
            'course_schedule_id' => $schedule->id,
            'status' => 'active',
            'fee' => $course->fee,
            'amount_paid' => $course->fee,
            'outstanding_balance' => 0,
            'policies_accepted' => true,
            'activated_at' => now(),
        ]);

        return [$student->fresh(), $enrolment];
    }

    private function admin(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole(Role::findOrCreate('Super Administrator'));

        return $admin;
    }
}
