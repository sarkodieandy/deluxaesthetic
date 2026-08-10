<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\AcademyShowcaseItem;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_page_shows_admin_managed_products(): void
    {
        $category = ProductCategory::create(['name' => 'Skincare', 'slug' => 'skincare', 'is_active' => true]);

        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Glow Serum',
            'slug' => 'glow-serum',
            'sku' => 'SERUM-001',
            'price' => 120,
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        $response = $this->get(route('web.store.index'));

        $response->assertOk();
        $response->assertSee('Glow Serum');
    }

    public function test_gallery_page_shows_gallery_and_before_after_items(): void
    {
        GalleryItem::create([
            'title' => 'Reception ambience',
            'slug' => 'reception-ambience',
            'type' => 'gallery',
            'image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'is_active' => true,
        ]);

        GalleryItem::create([
            'title' => 'Skin protocol result',
            'slug' => 'skin-protocol-result',
            'type' => 'before_after',
            'before_image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'after_image_path' => 'assets/web/images/hero/spa-treatment-room.jpg',
            'is_active' => true,
        ]);

        $response = $this->get(route('web.gallery'));

        $response->assertOk();
        $response->assertSee('Reception ambience');
        $response->assertSee('Skin protocol result');
        $response->assertSee('ba-compare', false);
    }

    public function test_homepage_shows_our_work_with_admin_gallery_items(): void
    {
        GalleryItem::create([
            'title' => 'Lash lift result',
            'slug' => 'lash-lift-result',
            'type' => 'before_after',
            'before_image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'after_image_path' => 'assets/web/images/hero/spa-treatment-room.jpg',
            'is_featured' => true,
            'is_active' => true,
        ]);

        GalleryItem::create([
            'title' => 'Studio detail',
            'slug' => 'studio-detail',
            'type' => 'gallery',
            'image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'is_featured' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('web.home'));

        $response->assertOk();
        $response->assertSee('our-work', false);
        $response->assertSee('Our Work');
        $response->assertSee('Studio detail');
        $response->assertSee('ba-compare', false);
    }

    public function test_treatment_pages_render_uploaded_images(): void
    {
        $category = TreatmentCategory::create(['name' => 'Facials', 'slug' => 'facials', 'is_active' => true]);

        $treatment = Treatment::create([
            'treatment_category_id' => $category->id,
            'name' => 'Clear Skin Facial',
            'slug' => 'clear-skin-facial',
            'short_description' => 'Clarity-focused treatment.',
            'duration_minutes' => 60,
            'price' => 250,
            'image_path' => 'assets/web/images/treatments/facial-care.jpg',
            'is_active' => true,
        ]);

        $index = $this->get(route('web.clinical.index'));
        $show = $this->get(route('web.treatments.show', $treatment->slug));

        $index->assertOk()->assertSee('facial-care.jpg');
        $show->assertOk()->assertSee('facial-care.jpg');
    }

    public function test_academy_page_shows_published_courses_fees_and_showcase_content(): void
    {
        $category = CourseCategory::create(['name' => 'Injectables', 'slug' => 'injectables', 'is_active' => true]);
        Course::create([
            'course_category_id' => $category->id,
            'name' => 'Botox Masterclass',
            'slug' => 'botox-masterclass',
            'delivery_mode' => 'physical',
            'duration_hours' => 12,
            'fee' => 3500,
            'is_active' => true,
        ]);
        AcademyShowcaseItem::create([
            'type' => 'student_story',
            'title' => 'Ama K.',
            'body' => 'The supervised practical session built my confidence.',
            'is_active' => true,
        ]);

        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertSee('Botox Masterclass')
            ->assertSee('GHS 3,500.00')
            ->assertSee('Ama K.')
            ->assertSee('The supervised practical session built my confidence.');
    }

    public function test_ceo_masterclass_packages_are_available_with_usd_prices_and_curriculum(): void
    {
        $this->assertDatabaseHas('courses', ['slug' => 'basic-aesthetics-class', 'currency' => 'USD', 'fee' => 1200]);
        $this->assertDatabaseHas('courses', ['slug' => 'advance-aesthetics-class', 'currency' => 'USD', 'fee' => 2000]);
        $this->assertDatabaseHas('courses', ['slug' => 'master-class-one', 'currency' => 'USD', 'fee' => 3500]);
        $this->assertDatabaseHas('courses', ['slug' => 'master-class-two', 'currency' => 'USD', 'fee' => 3500]);

        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertSee('US$1,200')
            ->assertSee('US$2,000')
            ->assertSee('US$3,500')
            ->assertSee('Migraine Botox')
            ->assertSee('PDO Face Lift')
            ->assertSee('ceo-academy-portrait.webp');
    }

    public function test_clinical_procedures_page_shows_published_before_and_after_work(): void
    {
        GalleryItem::create([
            'title' => 'Injectable result',
            'slug' => 'injectable-result',
            'type' => 'before_after',
            'before_image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'after_image_path' => 'assets/web/images/hero/spa-treatment-room.jpg',
            'is_active' => true,
        ]);

        $this->get(route('web.clinical.index'))
            ->assertOk()
            ->assertSee('Before &amp; after', false)
            ->assertSee('Injectable result');
    }
}
