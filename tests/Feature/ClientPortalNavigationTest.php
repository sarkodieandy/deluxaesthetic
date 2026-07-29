<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientPortalNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_client_can_open_all_portal_modules(): void
    {
        $client = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $client->assignRole(Role::findOrCreate('Client'));
        \App\Models\ClientProfile::query()->create([
            'user_id' => $client->id,
            'referral_code' => 'CLI12345',
        ]);

        foreach ([
            'client.dashboard',
            'client.appointments.index',
            'client.consultations.index',
            'client.payments.index',
            'client.orders.index',
            'client.loyalty.index',
            'client.notifications.index',
            'client.profile.edit',
        ] as $routeName) {
            $this->actingAs($client)
                ->get(route($routeName))
                ->assertOk();
        }
    }
}
