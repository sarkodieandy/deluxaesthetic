<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryLocationCollectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gallery_groups_photos_and_filters_by_country(): void
    {
        GalleryItem::create([
            'title' => 'Global clinic moment',
            'slug' => 'global-clinic-moment',
            'type' => 'gallery',
            'location_group' => 'global',
            'image_path' => 'https://cdn.example.test/global.jpg',
            'is_active' => true,
        ]);

        GalleryItem::create([
            'title' => 'Accra training day',
            'slug' => 'accra-training-day',
            'type' => 'gallery',
            'location_group' => 'ghana',
            'image_path' => 'https://cdn.example.test/ghana.jpg',
            'is_active' => true,
        ]);

        GalleryItem::create([
            'title' => 'Dakar masterclass',
            'slug' => 'dakar-masterclass',
            'type' => 'gallery',
            'location_group' => 'senegal',
            'image_path' => 'https://cdn.example.test/senegal.jpg',
            'is_active' => true,
        ]);

        $this->get(route('web.gallery'))
            ->assertOk()
            ->assertSee('gallery-cinema', false)
            ->assertSee('Stories from every destination.')
            ->assertSee('Global clinic moment')
            ->assertSee('Accra training day')
            ->assertSee('Dakar masterclass');

        $this->get(route('web.gallery', ['collection' => 'ghana']))
            ->assertOk()
            ->assertSee('Accra training day')
            ->assertSee('id="collection-ghana"', false)
            ->assertDontSee('id="collection-global"', false)
            ->assertDontSee('id="collection-senegal"', false);
    }

    public function test_admin_can_assign_and_filter_a_gallery_collection(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'title' => 'Cameroon academy visit',
            'type' => 'gallery',
            'location_group' => 'cameroon',
            'image_url' => 'https://cdn.example.test/cameroon.jpg',
            'is_active' => '1',
        ])->assertRedirect(route('admin.gallery.index'));

        $this->assertDatabaseHas('gallery_items', [
            'slug' => 'cameroon-academy-visit',
            'location_group' => 'cameroon',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.gallery.index', ['collection' => 'cameroon']))
            ->assertOk()
            ->assertSee('Cameroon academy visit')
            ->assertSee('Cameroon');
    }

    public function test_existing_admin_submissions_default_to_global_gallery(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'title' => 'General brand photograph',
            'type' => 'gallery',
            'image_url' => 'https://cdn.example.test/general.jpg',
            'is_active' => '1',
        ])->assertRedirect(route('admin.gallery.index'));

        $this->assertDatabaseHas('gallery_items', [
            'slug' => 'general-brand-photograph',
            'location_group' => GalleryItem::DEFAULT_LOCATION_GROUP,
        ]);
    }

    public function test_admin_gallery_exposes_collection_overview_and_prefills_destination_upload(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        GalleryItem::create([
            'title' => 'Dakar student practical',
            'slug' => 'dakar-student-practical',
            'type' => 'gallery',
            'location_group' => 'senegal',
            'image_path' => 'https://cdn.example.test/dakar.jpg',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.gallery.index'))
            ->assertOk()
            ->assertSee('Country collections')
            ->assertSee('Dakar student practical')
            ->assertSee(route('admin.gallery.create', ['type' => 'gallery', 'collection' => 'senegal']));

        $this->actingAs($admin)
            ->get(route('admin.gallery.create', ['type' => 'gallery', 'collection' => 'senegal']))
            ->assertOk()
            ->assertSee('Choose the destination collection')
            ->assertSee('value="senegal" checked', false);
    }
}
