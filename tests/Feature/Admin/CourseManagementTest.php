<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_updates_masterclass_currency_price_and_public_curriculum(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'category_name' => 'Injectable Masterclasses',
            'name' => 'Basic Class',
            'description' => 'Updated foundation programme.',
            'entry_requirements' => '',
            'duration_hours' => 8,
            'venue' => 'Accra',
            'max_students' => 12,
            'waiting_list_capacity' => 4,
            'currency' => 'USD',
            'sort_order' => 5,
            'fee' => 1450,
            'deposit_amount' => 300,
            'curriculum_outline' => "Crows Feet Botox | Anatomy and assessment | Safe injection technique\nSkin Boosters | Product selection | Aftercare",
            'is_featured' => '1',
            'is_active' => '1',
        ])->assertRedirect(route('admin.courses.index'));

        $course->refresh();
        $this->assertSame('USD', $course->currency);
        $this->assertSame(5, $course->sort_order);
        $this->assertSame('1450.00', $course->fee);
        $this->assertSame('Crows Feet Botox', $course->learning_outcomes[0]['name']);

        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertSee('US$1,450')
            ->assertSee('Safe injection technique');
    }

    public function test_admin_can_replace_and_remove_a_managed_course_image_safely(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();

        $payload = [
            'category_name' => 'Injectable Masterclasses',
            'name' => $course->name,
            'description' => $course->description,
            'duration_hours' => 8,
            'venue' => 'Accra',
            'max_students' => 12,
            'waiting_list_capacity' => 4,
            'currency' => 'USD',
            'sort_order' => 10,
            'fee' => 1200,
            'curriculum_outline' => 'Crows Feet Botox | Anatomy | Safe technique',
            'is_featured' => '1',
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('basic-class.jpg', 1200, 800),
        ];

        $this->actingAs($admin)
            ->put(route('admin.courses.update', $course), $payload)
            ->assertRedirect(route('admin.courses.index'));

        $managedPath = $course->fresh()->image_path;
        $this->assertNotNull($managedPath);
        Storage::disk('public')->assertExists($managedPath);

        unset($payload['image']);
        $payload['remove_image'] = '1';
        $this->put(route('admin.courses.update', $course), $payload)
            ->assertRedirect(route('admin.courses.index'));

        $this->assertNull($course->fresh()->image_path);
        Storage::disk('public')->assertMissing($managedPath);
    }

    public function test_admin_can_hide_a_course_from_the_public_academy(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'category_name' => $course->category->name,
            'name' => $course->name,
            'description' => $course->description,
            'duration_hours' => $course->duration_hours,
            'max_students' => $course->max_students,
            'waiting_list_capacity' => $course->waiting_list_capacity,
            'currency' => $course->currency,
            'sort_order' => $course->sort_order,
            'fee' => $course->fee,
        ])->assertRedirect(route('admin.courses.index'));

        $this->assertFalse($course->fresh()->is_active);
        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertDontSee($course->name);
    }

    public function test_course_with_student_history_must_be_unpublished_instead_of_deleted(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');
        $course = Course::query()->where('slug', 'basic-aesthetics-class')->firstOrFail();
        $schedule = CourseSchedule::query()->create([
            'course_id' => $course->id,
            'starts_on' => now()->addWeek()->toDateString(),
            'ends_on' => now()->addWeeks(2)->toDateString(),
            'capacity' => 10,
            'enrolled_count' => 1,
            'is_active' => true,
        ]);
        $studentUser = User::factory()->create();
        $student = StudentProfile::query()->create([
            'user_id' => $studentUser->id,
            'student_number' => 'STU-HISTORY-001',
        ]);
        $enrolment = Enrolment::query()->create([
            'reference' => 'ENR-HISTORY-001',
            'student_profile_id' => $student->id,
            'course_id' => $course->id,
            'course_schedule_id' => $schedule->id,
            'status' => 'active',
            'fee' => $course->fee,
            'amount_paid' => 0,
            'outstanding_balance' => $course->fee,
            'currency' => $course->currency,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.courses.destroy', $course))
            ->assertSessionHasErrors('course');

        $this->assertNull($course->fresh()->deleted_at);

        $course->delete();
        $this->assertSame($course->name, $enrolment->fresh()->course?->name);
    }
}
