<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginPortalRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_student_login_redirects_to_student_portal(): void
    {
        $student = User::factory()->create([
            'email' => 'student-login@example.com',
            'password' => bcrypt('Password1!'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole('Student');
        \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-2026-0099',
        ]);

        $this->post(route('login'), [
            'email' => 'student-login@example.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('student.dashboard'));
    }

    public function test_student_login_ignores_intended_admin_url(): void
    {
        $student = User::factory()->create([
            'email' => 'student-login@example.com',
            'password' => bcrypt('Password1!'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole('Student');
        \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-2026-0099',
        ]);

        $this->withSession(['url.intended' => url('/admin')])
            ->post(route('login'), [
                'email' => 'student-login@example.com',
                'password' => 'Password1!',
            ])
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin-login@example.com',
            'password' => bcrypt('Password1!'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Clinic Administrator');

        $this->post(route('login'), [
            'email' => 'admin-login@example.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_client_registration_redirects_to_client_portal(): void
    {
        Role::findOrCreate('Client');

        $this->post(route('register'), [
            'name' => 'Client User',
            'email' => 'client-new@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])->assertRedirect(route('client.dashboard'));
    }
}
