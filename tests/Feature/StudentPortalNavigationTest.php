<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentPortalNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * @return array<string, string>
     */
    public static function studentModuleRoutes(): array
    {
        return [
            'dashboard' => 'student.dashboard',
            'course' => 'student.course.show',
            'calendar' => 'student.calendar.index',
            'materials' => 'student.materials.index',
            'attendance' => 'student.attendance.index',
            'assignments' => 'student.assignments.index',
            'assessments' => 'student.assessments.index',
            'payments' => 'student.payments.index',
            'certificates' => 'student.certificates.index',
            'notifications' => 'student.notifications.index',
            'support' => 'student.support.index',
            'profile' => 'student.profile.edit',
            'security' => 'student.security.index',
        ];
    }

    public function test_student_can_open_all_portal_modules(): void
    {
        $student = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole(Role::findOrCreate('Student'));
        \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-NAV-001',
        ]);

        foreach (self::studentModuleRoutes() as $routeName) {
            $this->actingAs($student)
                ->get(route($routeName))
                ->assertOk();
        }
    }
}
