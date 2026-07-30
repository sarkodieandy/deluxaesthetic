<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOrderTrendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_dashboard_displays_order_and_paid_revenue_trends(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');

        Order::create([
            'number' => 'DLX-TREND-001',
            'user_id' => $admin->id,
            'status' => 'processing',
            'payment_status' => 'paid',
            'fulfillment_type' => 'pickup',
            'subtotal' => 450,
            'grand_total' => 450,
            'currency' => 'GHS',
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard', ['range' => 30]))
            ->assertOk()
            ->assertSee('Store performance trends')
            ->assertSee('Paid revenue')
            ->assertSee('GHS 450.00');
    }
}
