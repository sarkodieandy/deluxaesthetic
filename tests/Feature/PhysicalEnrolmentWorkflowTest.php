<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Academy\PhysicalEnrolmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
            'amount_paid' => 500,
            'enrolment_date' => now()->toDateString(),
            'policies_accepted' => true,
        ], $admin);

        $this->assertDatabaseHas('enrolments', ['id' => $enrolment->id, 'status' => 'application_pending']);

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
}
