<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_and_update_their_profile(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.profile.edit'))
            ->assertOk()
            ->assertSee('Personal information');

        $this->actingAs($admin)->patch(route('admin.profile.update'), [
            'name' => 'Clinic Director',
            'email' => 'director@example.com',
            'phone' => '+233501234567',
            'locale' => 'en',
        ])->assertSessionHas('status', 'profile-updated');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Clinic Director',
            'email' => 'director@example.com',
            'phone' => '+233501234567',
        ]);
    }

    public function test_admin_can_view_security_and_change_password(): void
    {
        $admin = $this->admin(['password' => Hash::make('old-password')]);

        $this->actingAs($admin)->get(route('admin.account.security'))
            ->assertOk()
            ->assertSee('Google sign-in')
            ->assertSee('Active sessions');

        $this->actingAs($admin)->put(route('password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('new-secure-password', $admin->fresh()->password));
    }

    public function test_super_admin_can_create_and_update_a_staff_account(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Front Desk',
            'email' => 'frontdesk@example.com',
            'phone' => '+233500000001',
            'role' => 'Receptionist',
            'locale' => 'en',
            'is_active' => '1',
            'password' => 'temporary-password',
            'password_confirmation' => 'temporary-password',
        ]);

        $staff = User::where('email', 'frontdesk@example.com')->firstOrFail();
        $response->assertRedirect(route('admin.users.edit', $staff));
        $this->assertTrue($staff->hasRole('Receptionist'));

        $this->actingAs($admin)->put(route('admin.users.update', $staff), [
            'name' => 'Front Desk Manager',
            'email' => 'frontdesk@example.com',
            'phone' => '',
            'role' => 'Support Agent',
            'locale' => 'en',
            'is_active' => '1',
            'password' => '',
            'password_confirmation' => '',
        ])->assertSessionHas('status', 'user-updated');

        $this->assertTrue($staff->fresh()->hasRole('Support Agent'));
    }

    public function test_non_super_admin_cannot_manage_a_super_admin(): void
    {
        $superAdmin = $this->admin();
        $clinicAdmin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $clinicAdmin->assignRole('Clinic Administrator');

        $this->actingAs($clinicAdmin)
            ->get(route('admin.users.edit', $superAdmin))
            ->assertForbidden();
    }

    private function admin(array $attributes = []): User
    {
        $admin = User::factory()->create(array_merge([
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));
        $admin->assignRole('Super Administrator');

        return $admin;
    }
}
