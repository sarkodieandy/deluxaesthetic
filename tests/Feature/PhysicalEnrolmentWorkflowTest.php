<?php

namespace Tests\Feature;

use App\Enums\EnrolmentStatus;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\Academy\PhysicalEnrolmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PhysicalEnrolmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_activate_physical_enrolment(): void
    {
        Permission::findOrCreate('students.create');
        Permission::findOrCreate('enrolments.create');
        Permission::findOrCreate('enrolments.activate');
        Permission::findOrCreate('enrolments.manage');

        $role = Role::findOrCreate('Super Administrator');
        $role->syncPermissions(['students.create', 'enrolments.create', 'enrolments.activate', 'enrolments.manage']);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole($role);

        $service = app(PhysicalEnrolmentService::class);
        $studentUser = $service->createStudentAccount([
            'name' => 'Portal Student',
            'email' => 'portal-student@example.com',
            'phone' => '+233200000001',
        ], $admin);

        $categoryId = DB::table('course_categories')->insertGetId([
            'name' => 'Aesthetics', 'slug' => 'aesthetics', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $courseId = DB::table('courses')->insertGetId([
            'course_category_id' => $categoryId,
            'name' => 'Course A', 'slug' => 'course-a', 'delivery_mode' => 'physical', 'duration_hours' => 8,
            'max_students' => 10, 'waiting_list_capacity' => 2, 'fee' => 1000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $scheduleId = DB::table('course_schedules')->insertGetId([
            'course_id' => $courseId, 'starts_on' => now()->toDateString(), 'ends_on' => now()->addWeek()->toDateString(),
            'capacity' => 10, 'enrolled_count' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $enrolment = $service->createPhysicalEnrolment($studentUser->studentProfile, [
            'course_id' => $courseId,
            'course_schedule_id' => $scheduleId,
            'fee' => 1000,
            'currency' => 'USD',
            'amount_paid' => 500,
            'enrolment_date' => now()->toDateString(),
            'policies_accepted' => true,
        ], $admin);

        $this->assertDatabaseHas('enrolments', ['id' => $enrolment->id, 'status' => 'application_pending', 'currency' => 'USD']);

        $service->activateEnrolment($enrolment, $admin, false);

        $this->assertDatabaseHas('enrolments', ['id' => $enrolment->id, 'status' => 'active']);
        $this->assertTrue($studentUser->fresh()->is_active);

        $studentUser->forceFill(['email_verified_at' => now()])->save();
        $studentUser->assignRole(Role::findOrCreate('Student'));
        $studentUser->studentProfile->update(['profile_completed_at' => now()]);

        $this->actingAs($studentUser->fresh())
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Course A');
    }

    public function test_cancelled_and_withdrawn_enrolments_cannot_overbook_a_full_schedule(): void
    {
        $admin = $this->staffWithPermissions('Clinic Administrator', ['enrolments.activate']);
        $service = app(PhysicalEnrolmentService::class);

        foreach ([EnrolmentStatus::Cancelled->value, EnrolmentStatus::Withdrawn->value] as $status) {
            [$courseId, $scheduleId] = $this->courseScheduleFixture(1, 1);
            $occupyingStudent = $this->studentProfile('occupied-'.$status.'@example.com');
            $targetStudent = $this->studentProfile('target-'.$status.'@example.com');

            $this->enrolmentFixture($occupyingStudent, $courseId, $scheduleId, EnrolmentStatus::Active->value);
            $target = $this->enrolmentFixture($targetStudent, $courseId, $scheduleId, $status);

            try {
                $service->activateEnrolment($target, $admin, false);
                $this->fail("The {$status} enrolment was activated despite the schedule being full.");
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('status', $exception->errors());
            }

            $this->assertSame($status, $target->fresh()->status);
            $this->assertSame(1, (int) DB::table('course_schedules')->where('id', $scheduleId)->value('enrolled_count'));
            $this->assertSame(1, Enrolment::query()
                ->where('course_schedule_id', $scheduleId)
                ->whereNotIn('status', [EnrolmentStatus::Cancelled->value, EnrolmentStatus::Withdrawn->value])
                ->count());
        }
    }

    public function test_manage_only_staff_can_activate_without_create_permission_and_capacity_is_reconciled(): void
    {
        Notification::fake();

        $manager = $this->staffWithPermissions('Trainer', ['enrolments.manage']);
        [$courseId, $scheduleId] = $this->courseScheduleFixture(2, 99);
        $occupyingStudent = $this->studentProfile('existing.student@example.com');
        $targetStudent = $this->studentProfile('restored.student@example.com');

        $this->enrolmentFixture($occupyingStudent, $courseId, $scheduleId, EnrolmentStatus::Active->value);
        $target = $this->enrolmentFixture(
            $targetStudent,
            $courseId,
            $scheduleId,
            EnrolmentStatus::Withdrawn->value,
        );

        $this->actingAs($manager)
            ->get(route('admin.enrolments.edit', $target))
            ->assertOk()
            ->assertSee('Activate &amp; invite to portal', false);

        $this->post(route('admin.physical-enrolment.activate', $target))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame(EnrolmentStatus::Active->value, $target->fresh()->status);
        $this->assertTrue($targetStudent->user->fresh()->is_active);
        $this->assertSame(2, (int) DB::table('course_schedules')->where('id', $scheduleId)->value('enrolled_count'));
    }

    public function test_activate_only_staff_can_use_activation_route_without_create_or_manage_permission(): void
    {
        Notification::fake();

        $activator = $this->staffWithPermissions('Clinic Administrator', ['enrolments.activate']);
        [$courseId, $scheduleId] = $this->courseScheduleFixture(1, 0);
        $student = $this->studentProfile('activate-only@example.com');
        $target = $this->enrolmentFixture(
            $student,
            $courseId,
            $scheduleId,
            EnrolmentStatus::ApplicationPending->value,
        );

        $this->actingAs($activator)
            ->post(route('admin.physical-enrolment.activate', $target))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertFalse($activator->can('enrolments.create'));
        $this->assertFalse($activator->can('enrolments.manage'));
        $this->assertSame(EnrolmentStatus::Active->value, $target->fresh()->status);
        $this->assertSame(1, (int) DB::table('course_schedules')->where('id', $scheduleId)->value('enrolled_count'));
    }

    public function test_terminal_enrolments_cannot_be_regressed_to_active(): void
    {
        $manager = $this->staffWithPermissions('Academic Manager', ['enrolments.manage']);
        $service = app(PhysicalEnrolmentService::class);

        foreach ([
            EnrolmentStatus::Completed->value,
            EnrolmentStatus::Graduated->value,
            EnrolmentStatus::CertificateIssued->value,
        ] as $status) {
            [$courseId, $scheduleId] = $this->courseScheduleFixture(3, 99);
            $student = $this->studentProfile('terminal-'.$status.'@example.com');
            $target = $this->enrolmentFixture($student, $courseId, $scheduleId, $status);

            try {
                $service->activateEnrolment($target, $manager, false);
                $this->fail("The {$status} enrolment was incorrectly reactivated.");
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('status', $exception->errors());
            }

            $this->assertSame($status, $target->fresh()->status);
            $this->assertFalse($student->user->fresh()->is_active);
            $this->assertSame(99, (int) DB::table('course_schedules')->where('id', $scheduleId)->value('enrolled_count'));
            $this->assertDatabaseCount('enrolment_status_histories', 0);
        }
    }

    public function test_editing_status_to_active_uses_the_activation_integrity_path(): void
    {
        Notification::fake();

        $manager = $this->staffWithPermissions('Trainer', ['enrolments.manage']);
        [$courseId, $scheduleId] = $this->courseScheduleFixture(2, 88);
        $student = $this->studentProfile('edit-activation@example.com');
        $target = $this->enrolmentFixture(
            $student,
            $courseId,
            $scheduleId,
            EnrolmentStatus::Withdrawn->value,
        );

        $this->actingAs($manager)
            ->put(route('admin.enrolments.update', $target), [
                'status' => EnrolmentStatus::Active->value,
                'amount_paid' => 250,
                'outstanding_balance' => 750,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $target->refresh();
        $this->assertSame(EnrolmentStatus::Active->value, $target->status);
        $this->assertNotNull($target->activated_at);
        $this->assertSame($manager->id, $target->activated_by);
        $this->assertSame('250.00', $target->amount_paid);
        $this->assertTrue($student->user->fresh()->is_active);
        $this->assertSame(1, (int) DB::table('course_schedules')->where('id', $scheduleId)->value('enrolled_count'));
        $this->assertDatabaseHas('enrolment_status_histories', [
            'enrolment_id' => $target->id,
            'from_status' => EnrolmentStatus::Withdrawn->value,
            'to_status' => EnrolmentStatus::Active->value,
        ]);
    }

    public function test_active_activation_is_idempotent_and_only_reconciles_the_counter(): void
    {
        $activator = $this->staffWithPermissions('Portal Activator', ['enrolments.activate']);
        [$courseId, $scheduleId] = $this->courseScheduleFixture(3, 45);
        $student = $this->studentProfile('already-active@example.com');
        $target = $this->enrolmentFixture(
            $student,
            $courseId,
            $scheduleId,
            EnrolmentStatus::Active->value,
        );

        app(PhysicalEnrolmentService::class)->activateEnrolment($target, $activator, false);

        $this->assertSame(EnrolmentStatus::Active->value, $target->fresh()->status);
        $this->assertSame(1, (int) DB::table('course_schedules')->where('id', $scheduleId)->value('enrolled_count'));
        $this->assertDatabaseCount('enrolment_status_histories', 0);
    }

    /** @param list<string> $permissions */
    private function staffWithPermissions(string $roleName, array $permissions): User
    {
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $role = Role::findOrCreate($roleName);
        $role->syncPermissions($permissions);
        $staff = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $staff->assignRole($role);

        return $staff;
    }

    /** @return array{0: int, 1: int} */
    private function courseScheduleFixture(int $capacity, int $enrolledCount): array
    {
        $suffix = str_replace('.', '-', uniqid('', true));
        $categoryId = DB::table('course_categories')->insertGetId([
            'name' => 'Aesthetics '.$suffix,
            'slug' => 'aesthetics-'.$suffix,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $courseId = DB::table('courses')->insertGetId([
            'course_category_id' => $categoryId,
            'name' => 'Capacity Course '.$suffix,
            'slug' => 'capacity-course-'.$suffix,
            'delivery_mode' => 'physical',
            'duration_hours' => 8,
            'max_students' => $capacity,
            'waiting_list_capacity' => 0,
            'fee' => 1000,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $scheduleId = DB::table('course_schedules')->insertGetId([
            'course_id' => $courseId,
            'starts_on' => now()->addMonth()->toDateString(),
            'ends_on' => now()->addMonth()->addWeek()->toDateString(),
            'capacity' => $capacity,
            'enrolled_count' => $enrolledCount,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$courseId, $scheduleId];
    }

    private function studentProfile(string $email): StudentProfile
    {
        $user = User::factory()->create([
            'email' => $email,
            'is_active' => false,
            'email_verified_at' => now(),
        ]);

        return StudentProfile::query()->create([
            'user_id' => $user->id,
            'student_number' => 'STU-'.strtoupper(substr(md5($email), 0, 12)),
        ]);
    }

    private function enrolmentFixture(
        StudentProfile $student,
        int $courseId,
        int $scheduleId,
        string $status,
    ): Enrolment {
        return Enrolment::query()->create([
            'reference' => 'ENR-'.strtoupper(substr(md5($student->id.'-'.$scheduleId), 0, 16)),
            'student_profile_id' => $student->id,
            'course_id' => $courseId,
            'course_schedule_id' => $scheduleId,
            'status' => $status,
            'fee' => 1000,
            'amount_paid' => 0,
            'outstanding_balance' => 1000,
            'currency' => 'USD',
        ]);
    }
}
