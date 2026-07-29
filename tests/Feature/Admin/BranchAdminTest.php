<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_clinic_administrator_can_create_and_list_branches(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $create = $this->actingAs($admin)->post(route('admin.branches.store'), [
            'name' => 'East Legon',
            'city' => 'Accra',
            'region' => 'Greater Accra',
            'phone' => '+233552248636',
            'is_active' => '1',
            'is_primary' => '1',
            'sort_order' => 1,
        ]);

        $create->assertRedirect(route('admin.branches.index'));

        $this->assertDatabaseHas('branches', [
            'name' => 'East Legon',
            'slug' => 'east-legon',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $index = $this->actingAs($admin)->get(route('admin.branches.index'));
        $index->assertOk()->assertSee('East Legon');
    }

    public function test_cannot_delete_primary_branch(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $branch = Branch::create([
            'name' => 'Main',
            'slug' => 'main',
            'is_active' => true,
            'is_primary' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.branches.destroy', $branch));

        $response->assertRedirect();
        $response->assertSessionHasErrors('branch');
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'deleted_at' => null]);
    }
}
