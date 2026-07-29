<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_admin_product_photo_is_saved_and_visible_on_store(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $category = ProductCategory::create([
            'name' => 'Body Care',
            'slug' => 'body-care',
            'is_active' => true,
        ]);

        $photo = UploadedFile::fake()->image('cream.jpg', 800, 800);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'product_category_id' => $category->id,
                'name' => 'Cream',
                'sku' => 'BODY-CREAM-01',
                'description' => 'very good product',
                'price' => 200,
                'stock_quantity' => 10,
                'is_active' => '1',
                'image' => $photo,
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('sku', 'BODY-CREAM-01')->first();
        $this->assertNotNull($product);

        $image = ProductImage::query()->where('product_id', $product->id)->first();
        $this->assertNotNull($image);
        Storage::disk('public')->assertExists($image->path);
        $this->assertNotNull($product->fresh(['images'])->imageUrl());

        $this->get(route('web.store.index'))
            ->assertOk()
            ->assertSee('Cream')
            ->assertSee('/storage/'.$image->path, false);
    }

    public function test_create_requires_a_product_photo(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $category = ProductCategory::create([
            'name' => 'Serums',
            'slug' => 'serums',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'product_category_id' => $category->id,
                'name' => 'Serum',
                'sku' => 'SERUM-01',
                'price' => 100,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors(['image']);
    }
}
