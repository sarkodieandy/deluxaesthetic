<?php

namespace Tests\Feature\Admin;

use App\Models\CourseMaterial;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseMaterialManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_a_file_for_one_students_enrolment(): void
    {
        Storage::fake('public');
        Permission::findOrCreate('materials.manage');
        $adminRole = Role::findOrCreate('Super Administrator');
        $adminRole->givePermissionTo('materials.manage');
        $studentRole = Role::findOrCreate('Student');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole($adminRole);
        [$courseId, $scheduleId] = $this->courseAndSchedule();
        $wendy = $this->student('Wendy', 'wendy@example.com', 'STU-001', $studentRole);
        $other = $this->student('Other Student', 'other@example.com', 'STU-002', $studentRole);
        $wendyEnrolment = $this->enrol($wendy, $courseId, $scheduleId, 'ENR-001');
        $this->enrol($other, $courseId, $scheduleId, 'ENR-002');

        $this->actingAs($admin)->post(route('admin.course-materials.store'), [
            'course_id' => $courseId,
            'enrolment_id' => $wendyEnrolment->id,
            'title' => 'Private practical guide',
            'type' => 'guide',
            'file' => UploadedFile::fake()->create('guide.pdf', 200, 'application/pdf'),
            'is_published' => '1',
        ])->assertRedirect();

        $material = CourseMaterial::query()->firstOrFail();
        Storage::disk('public')->assertExists($material->file_path);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $wendy->id]);

        $this->actingAs($wendy)->get(route('student.materials.index'))
            ->assertOk()->assertSee('Private practical guide');
        $this->actingAs($wendy)->get(route('student.materials.download', $material))->assertOk();

        $this->actingAs($other)->get(route('student.materials.index'))
            ->assertOk()->assertDontSee('Private practical guide');
        $this->actingAs($other)->get(route('student.materials.download', $material))->assertForbidden();
    }

    private function courseAndSchedule(): array
    {
        $categoryId = DB::table('course_categories')->insertGetId([
            'name' => 'Aesthetics', 'slug' => 'aesthetics', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $courseId = DB::table('courses')->insertGetId([
            'course_category_id' => $categoryId, 'name' => 'Botox Masterclass', 'slug' => 'botox-masterclass',
            'delivery_mode' => 'physical', 'duration_hours' => 8, 'max_students' => 10,
            'waiting_list_capacity' => 2, 'fee' => 1000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $scheduleId = DB::table('course_schedules')->insertGetId([
            'course_id' => $courseId, 'starts_on' => now()->toDateString(), 'ends_on' => now()->addWeek()->toDateString(),
            'capacity' => 10, 'enrolled_count' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return [$courseId, $scheduleId];
    }

    private function student(string $name, string $email, string $number, Role $role): User
    {
        $user = User::factory()->create(['name' => $name, 'email' => $email, 'is_active' => true]);
        $user->assignRole($role);
        StudentProfile::query()->create([
            'user_id' => $user->id, 'student_number' => $number, 'profile_completed_at' => now(),
        ]);

        return $user->fresh();
    }

    private function enrol(User $student, int $courseId, int $scheduleId, string $reference): Enrolment
    {
        return Enrolment::query()->create([
            'reference' => $reference, 'student_profile_id' => $student->studentProfile->id,
            'course_id' => $courseId, 'course_schedule_id' => $scheduleId, 'status' => 'active',
            'fee' => 1000, 'amount_paid' => 1000, 'outstanding_balance' => 0,
            'policies_accepted' => true, 'activated_at' => now(),
        ]);
    }
}
