<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Database\Seeders\RolePermissionSeeder;
use Tests\TestCase;

class StudentPortalRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_academy_nav_opens_student_portal_registration(): void
    {
        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertSee(route('web.academy.student-portal.create'));

        $this->get(route('web.academy.student-portal.create'))
            ->assertOk()
            ->assertSee(__('web.student_portal.form_title'));
    }

    public function test_guest_can_register_student_portal_account(): void
    {
        Role::findOrCreate('Student');

        $response = $this->post(route('web.academy.student-portal.store'), [
            'name' => 'Ama Mensah',
            'email' => 'ama.student@example.com',
            'phone' => '+233200000099',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'privacy_consent' => '1',
        ]);

        $response->assertRedirect(route('student.dashboard'));

        $user = User::query()->where('email', 'ama.student@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Student'));
        $this->assertNotNull($user->studentProfile);
        $this->assertNotNull($user->studentProfile->profile_completed_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_normalizes_email_and_notifies_admin(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Super Administrator');

        $this->post(route('web.academy.student-portal.store'), [
            'name' => '  Akosua Boateng  ',
            'email' => 'AKOSUA.STUDENT@EXAMPLE.COM',
            'phone' => ' +233200000100 ',
            'password' => 'student123',
            'password_confirmation' => 'student123',
            'privacy_consent' => '1',
        ])->assertRedirect(route('student.dashboard'));

        $student = User::query()->where('email', 'akosua.student@example.com')->firstOrFail();
        $this->assertSame('Akosua Boateng', $student->name);
        $this->assertTrue($student->hasRole('Student'));
        $this->assertFalse($student->hasAnyRole(config('admin.roles')));
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_logged_in_student_is_redirected_from_academy_to_dashboard(): void
    {
        $student = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole(Role::findOrCreate('Student'));
        \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-2026-0001',
        ]);

        $this->actingAs($student)
            ->get(route('web.academy.index'))
            ->assertRedirect(route('student.dashboard'));
    }
}
