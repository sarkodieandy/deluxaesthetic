<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\CourseEnquiry;
use App\Models\CourseSchedule;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AcademyAdmissionsHandoffTest extends TestCase
{
    use RefreshDatabase;

    public function test_enquiry_handoff_preselects_the_registered_student_course_and_matching_schedule(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');

        $student = User::factory()->create(['is_active' => false, 'email_verified_at' => now()]);
        $student->assignRole(Role::findOrCreate('Student'));
        $profile = StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-2026-9001',
        ]);

        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $schedule = CourseSchedule::query()->create([
            'course_id' => $course->id,
            'starts_on' => now()->addWeek()->toDateString(),
            'ends_on' => now()->addWeeks(2)->toDateString(),
            'capacity' => 12,
            'is_active' => true,
        ]);
        $enquiry = CourseEnquiry::query()->create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'full_name' => $student->name,
            'email' => $student->email,
            'phone' => '+233200009001',
            'message' => 'I would like this physical course.',
            'privacy_consent' => true,
            'status' => 'contacted',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.physical-enrolment.create', ['enquiry' => $enquiry->id]))
            ->assertOk()
            ->assertSee('When this page is opened from an enquiry')
            ->assertSee($course->name)
            ->assertSee($student->name);

        $html = $response->getContent();
        $this->assertMatchesRegularExpression('/<option value="'.preg_quote((string) $profile->id, '/').'"\s+selected>/', $html);
        $this->assertMatchesRegularExpression('/<option value="'.preg_quote((string) $course->id, '/').'"[^>]*\sselected>/', $html);
        $this->assertMatchesRegularExpression('/<option value="'.preg_quote((string) $schedule->id, '/').'"[^>]*\sselected>/', $html);
        $this->assertMatchesRegularExpression('/<option value="'.preg_quote((string) $enquiry->id, '/').'"\s+selected>/', $html);
    }

    public function test_physical_enrolment_rejects_a_schedule_from_another_course(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');

        $student = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $student->assignRole(Role::findOrCreate('Student'));
        $profile = StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-2026-9002',
        ]);

        $selectedCourse = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $otherCourse = Course::query()->where('slug', 'master-class-two')->firstOrFail();
        $wrongSchedule = CourseSchedule::query()->create([
            'course_id' => $otherCourse->id,
            'starts_on' => now()->addMonth()->toDateString(),
            'ends_on' => now()->addMonth()->addWeek()->toDateString(),
            'capacity' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.physical-enrolment.store'), [
            'student_profile_id' => $profile->id,
            'course_id' => $selectedCourse->id,
            'course_schedule_id' => $wrongSchedule->id,
            'fee' => $selectedCourse->fee,
            'currency' => $selectedCourse->currency,
            'amount_paid' => 0,
            'enrolment_date' => now()->toDateString(),
        ])->assertSessionHasErrors('course_schedule_id');

        $this->assertDatabaseCount('enrolments', 0);
    }

    public function test_enquiry_cannot_be_converted_for_another_student_or_reused(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');

        [$applicant, $applicantProfile] = $this->student('Applicant One', 'applicant.one@example.com', 'STU-2026-9101');
        [, $otherProfile] = $this->student('Applicant Two', 'applicant.two@example.com', 'STU-2026-9102');
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $schedule = $this->schedule($course);
        $enquiry = CourseEnquiry::query()->create([
            'course_id' => $course->id,
            'user_id' => $applicant->id,
            'full_name' => $applicant->name,
            'email' => $applicant->email,
            'phone' => '+233200009101',
            'privacy_consent' => true,
            'status' => 'contacted',
        ]);

        $payload = [
            'student_profile_id' => $otherProfile->id,
            'course_id' => $course->id,
            'course_schedule_id' => $schedule->id,
            'course_enquiry_id' => $enquiry->id,
            'fee' => $course->fee,
            'currency' => $course->currency,
            'amount_paid' => 0,
            'enrolment_date' => now()->toDateString(),
        ];

        $this->actingAs($admin)->post(route('admin.physical-enrolment.store'), $payload)
            ->assertSessionHasErrors('course_enquiry_id');
        $this->assertDatabaseCount('enrolments', 0);

        $payload['student_profile_id'] = $applicantProfile->id;
        $this->post(route('admin.physical-enrolment.store'), $payload)
            ->assertRedirect();
        $this->assertDatabaseCount('enrolments', 1);

        $this->post(route('admin.physical-enrolment.store'), $payload)
            ->assertSessionHasErrors('course_enquiry_id');
        $this->assertDatabaseCount('enrolments', 1);
    }

    public function test_receptionist_records_pending_enrolment_without_partial_activation(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $receptionist = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $receptionist->assignRole('Receptionist');
        [, $profile] = $this->student('Pending Student', 'pending.student@example.com', 'STU-2026-9201');
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $schedule = $this->schedule($course);

        $this->actingAs($receptionist)->get(route('admin.physical-enrolment.create'))
            ->assertOk()
            ->assertDontSee('name="activate_now"', false)
            ->assertSee('must approve portal access afterward');

        $payload = [
            'student_profile_id' => $profile->id,
            'course_id' => $course->id,
            'course_schedule_id' => $schedule->id,
            'fee' => $course->fee,
            'currency' => $course->currency,
            'amount_paid' => 0,
            'enrolment_date' => now()->toDateString(),
        ];

        $this->post(route('admin.physical-enrolment.store'), $payload + [
            'activate_now' => '1',
            'status' => 'active',
        ])
            ->assertSessionHasErrors('activate_now');
        $this->assertDatabaseCount('enrolments', 0);

        $this->post(route('admin.physical-enrolment.store'), $payload + ['status' => 'active'])
            ->assertRedirect(route('admin.physical-enrolment.create'));
        $this->assertDatabaseHas('enrolments', [
            'student_profile_id' => $profile->id,
            'status' => 'application_pending',
        ]);
    }

    public function test_schedule_capacity_and_duplicate_student_assignment_are_enforced(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');
        [, $firstProfile] = $this->student('First Student', 'first.capacity@example.com', 'STU-2026-9301');
        [, $secondProfile] = $this->student('Second Student', 'second.capacity@example.com', 'STU-2026-9302');
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $schedule = $this->schedule($course);
        $schedule->update(['capacity' => 1]);

        $payload = $this->enrolmentPayload($firstProfile, $course, $schedule);
        $this->actingAs($admin)->post(route('admin.physical-enrolment.store'), $payload)->assertRedirect();
        $this->assertSame(1, $schedule->fresh()->enrolled_count);

        $this->post(route('admin.physical-enrolment.store'), $payload)
            ->assertSessionHasErrors('student_profile_id');
        $this->assertDatabaseCount('enrolments', 1);
        $this->assertSame(1, $schedule->fresh()->enrolled_count);

        $this->post(
            route('admin.physical-enrolment.store'),
            $this->enrolmentPayload($secondProfile, $course, $schedule),
        )->assertSessionHasErrors('course_schedule_id');
        $this->assertDatabaseCount('enrolments', 1);
        $this->assertSame(1, $schedule->fresh()->enrolled_count);
    }

    public function test_cancelling_and_restoring_an_enrolment_reconciles_schedule_capacity(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');
        [, $profile] = $this->student('Capacity Student', 'capacity.status@example.com', 'STU-2026-9401');
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $schedule = $this->schedule($course);

        $this->actingAs($admin)->post(
            route('admin.physical-enrolment.store'),
            $this->enrolmentPayload($profile, $course, $schedule),
        )->assertRedirect();

        $enrolment = Enrolment::query()->firstOrFail();
        $this->assertSame(1, $schedule->fresh()->enrolled_count);

        $this->put(route('admin.enrolments.update', $enrolment), [
            'status' => 'cancelled',
            'amount_paid' => 0,
            'outstanding_balance' => $enrolment->outstanding_balance,
        ])->assertRedirect();
        $this->assertSame(0, $schedule->fresh()->enrolled_count);

        $this->put(route('admin.enrolments.update', $enrolment), [
            'status' => 'active',
            'amount_paid' => 0,
            'outstanding_balance' => $enrolment->outstanding_balance,
        ])->assertRedirect();
        $this->assertSame(1, $schedule->fresh()->enrolled_count);
    }

    /** @return array{0: User, 1: StudentProfile} */
    private function student(string $name, string $email, string $number): array
    {
        $user = User::factory()->create(['name' => $name, 'email' => $email, 'is_active' => false]);
        $user->assignRole(Role::findOrCreate('Student'));
        $profile = StudentProfile::query()->create(['user_id' => $user->id, 'student_number' => $number]);

        return [$user, $profile];
    }

    private function schedule(Course $course): CourseSchedule
    {
        return CourseSchedule::query()->create([
            'course_id' => $course->id,
            'starts_on' => now()->addMonth()->toDateString(),
            'ends_on' => now()->addMonth()->addWeek()->toDateString(),
            'capacity' => 10,
            'is_active' => true,
        ]);
    }

    /** @return array<string, mixed> */
    private function enrolmentPayload(StudentProfile $profile, Course $course, CourseSchedule $schedule): array
    {
        return [
            'student_profile_id' => $profile->id,
            'course_id' => $course->id,
            'course_schedule_id' => $schedule->id,
            'fee' => $course->fee,
            'currency' => $course->currency,
            'amount_paid' => 0,
            'enrolment_date' => now()->toDateString(),
        ];
    }
}
