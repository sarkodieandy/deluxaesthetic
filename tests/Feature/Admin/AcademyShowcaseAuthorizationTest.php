<?php

namespace Tests\Feature\Admin;

use App\Models\AcademyShowcaseItem;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademyShowcaseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_staff_with_courses_view_cannot_mutate_academy_showcase_items(): void
    {
        $initialCount = AcademyShowcaseItem::query()->count();
        $trainer = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $trainer->assignRole('Trainer');

        $item = AcademyShowcaseItem::create([
            'type' => 'training_country',
            'title' => 'Test Training Region',
            'body' => 'Hands-on academy training in Accra.',
            'is_active' => true,
        ]);

        $this->actingAs($trainer)
            ->get(route('admin.academy-showcase.index'))
            ->assertOk()
            ->assertSee('Test Training Region')
            ->assertDontSee('Add showcase item')
            ->assertDontSee('Delete');

        $this->get(route('admin.academy-showcase.create'))->assertForbidden();
        $this->post(route('admin.academy-showcase.store'), $this->payload('Cameroon'))->assertForbidden();
        $this->get(route('admin.academy-showcase.edit', $item))->assertForbidden();
        $this->put(route('admin.academy-showcase.update', $item), $this->payload('Changed'))->assertForbidden();
        $this->delete(route('admin.academy-showcase.destroy', $item))->assertForbidden();

        $this->assertDatabaseCount('academy_showcase_items', $initialCount + 1);
        $this->assertDatabaseHas('academy_showcase_items', [
            'id' => $item->id,
            'title' => 'Test Training Region',
        ]);
    }

    public function test_staff_with_courses_update_can_manage_academy_showcase_items(): void
    {
        $admin = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Super Administrator');

        $this->actingAs($admin)
            ->get(route('admin.academy-showcase.create'))
            ->assertOk();

        $this->post(route('admin.academy-showcase.store'), $this->payload('Test Regional Delivery'))
            ->assertRedirect(route('admin.academy-showcase.index'));

        $item = AcademyShowcaseItem::query()->where('title', 'Test Regional Delivery')->firstOrFail();

        $this->get(route('admin.academy-showcase.edit', $item))->assertOk();
        $this->put(route('admin.academy-showcase.update', $item), $this->payload("Cote d'Ivoire"))
            ->assertRedirect(route('admin.academy-showcase.index'));

        $this->assertDatabaseHas('academy_showcase_items', [
            'id' => $item->id,
            'title' => "Cote d'Ivoire",
        ]);

        $this->delete(route('admin.academy-showcase.destroy', $item))->assertRedirect();
        $this->assertDatabaseMissing('academy_showcase_items', ['id' => $item->id]);
    }

    private function payload(string $title): array
    {
        return [
            'type' => 'training_country',
            'title' => $title,
            'body' => 'Verified academy training activity.',
            'sort_order' => 10,
            'is_active' => '1',
        ];
    }
}
