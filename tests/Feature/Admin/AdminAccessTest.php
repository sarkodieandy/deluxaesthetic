<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_client_cannot_access_admin_dashboard(): void
    {
        $client = User::factory()->create(['is_active' => true]);
        $client->assignRole('Client');

        $response = $this->actingAs($client)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_clinic_administrator_can_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Clinic Administrator');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
    }
}
