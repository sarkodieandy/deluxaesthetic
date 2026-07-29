<?php

namespace Tests\Feature\Admin;

use App\Models\GalleryItem;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryBeforeAfterUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_admin_can_upload_before_and_after_images(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $before = UploadedFile::fake()->image('before.jpg', 1200, 800);
        $after = UploadedFile::fake()->image('after.jpg', 1200, 800);

        $response = $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'title' => 'Lash lift case',
            'type' => 'before_after',
            'before_image' => $before,
            'after_image' => $after,
            'is_active' => '1',
            'is_featured' => '1',
            'sort_order' => 5,
        ]);

        $response->assertRedirect(route('admin.gallery.index'));

        $item = GalleryItem::query()->where('slug', 'lash-lift-case')->first();
        $this->assertNotNull($item);
        $this->assertSame('before_after', $item->type);
        $this->assertNotNull($item->before_image_path);
        $this->assertNotNull($item->after_image_path);
        $this->assertNull($item->image_path);

        Storage::disk('public')->assertExists($item->before_image_path);
        Storage::disk('public')->assertExists($item->after_image_path);

        $this->assertNotNull($item->beforeImageUrl());
        $this->assertStringStartsWith('/storage/', $item->beforeImageUrl());
    }

    public function test_before_and_after_requires_both_images_on_create(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Content Manager');

        $response = $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'title' => 'Incomplete case',
            'type' => 'before_after',
            'before_image' => UploadedFile::fake()->image('before.jpg'),
        ]);

        $response->assertSessionHasErrors(['after_image']);
        $this->assertDatabaseCount('gallery_items', 0);
    }

    public function test_admin_can_create_before_and_after_using_image_urls(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $response = $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'title' => 'URL comparison',
            'type' => 'before_after',
            'before_image_url' => 'https://cdn.example.test/before.jpg',
            'after_image_url' => 'https://cdn.example.test/after.jpg',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.gallery.index'));

        $item = GalleryItem::query()->where('slug', 'url-comparison')->first();
        $this->assertNotNull($item);
        $this->assertSame('https://cdn.example.test/before.jpg', $item->before_image_path);
        $this->assertSame('https://cdn.example.test/after.jpg', $item->after_image_path);
        $this->assertTrue($item->hasBeforeAfterPair());
    }
}
