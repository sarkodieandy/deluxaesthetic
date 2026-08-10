<?php

namespace Tests\Feature;

use App\Models\AcademyShowcaseItem;
use App\Models\GalleryItem;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedesignedHomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_showcases_the_three_divisions_and_new_hero_copy(): void
    {
        $response = $this->get(route('web.home'));

        $response->assertOk();
        $response->assertSee('Expert Injectable Care. Professional Training. Premium Products.');
        $response->assertSee('Three pillars. One trusted platform.');
        $response->assertSee('Clinical Procedures');
        $response->assertSee('Academy');
        $response->assertSee('Products');
        $response->assertSee('Shop Products');
    }

    public function test_homepage_shows_the_verified_west_africa_training_footprint(): void
    {
        $response = $this->get(route('web.home'));

        $response->assertOk();
        $response->assertSee('5 countries.');
        $response->assertSee('Ghana');
        $response->assertSee('Cameroon');
        $response->assertSee('Côte d’Ivoire');
        $response->assertSee('Senegal');
        $response->assertSee('Benin Republic');
        $response->assertSee('assets/web/flags/ghana.svg');
        $response->assertSee('ceo-academy-portrait.webp');
        $response->assertSee('hero-botox.webp');
    }

    public function test_homepage_countries_follow_admin_content_visibility_and_order(): void
    {
        AcademyShowcaseItem::query()->where('type', 'training_country')->where('title', 'Cameroon')->firstOrFail()->update([
            'subtitle' => 'Regional masterclasses',
            'body' => 'A confirmed programme for aesthetics professionals in Cameroon.',
        ]);

        AcademyShowcaseItem::query()->where('type', 'training_country')->where('title', 'Senegal')->firstOrFail()->update([
            'body' => 'This unpublished country description must remain private.',
            'is_active' => false,
        ]);

        AcademyShowcaseItem::query()->create([
            'type' => 'training_country',
            'title' => 'Togo',
            'subtitle' => 'New managed destination',
            'body' => 'A newly published Academy training destination.',
            'sort_order' => 35,
            'is_active' => true,
        ]);

        $response = $this->get(route('web.home'));

        $response
            ->assertOk()
            ->assertSee('Regional masterclasses')
            ->assertSee('A confirmed programme for aesthetics professionals in Cameroon.')
            ->assertSee('New managed destination')
            ->assertSee('A newly published Academy training destination.')
            ->assertDontSee('This unpublished country description must remain private.')
            ->assertDontSee('Senegal');
    }

    public function test_homepage_only_surfaces_published_clinical_content_and_supports_remote_images(): void
    {
        $visibleCategory = TreatmentCategory::query()->create([
            'name' => 'Visible Clinical Care',
            'slug' => 'visible-clinical-care',
            'is_active' => true,
        ]);
        $hiddenCategory = TreatmentCategory::query()->create([
            'name' => 'Hidden Clinical Care',
            'slug' => 'hidden-clinical-care',
            'is_active' => false,
        ]);
        $visible = Treatment::query()->create([
            'treatment_category_id' => $visibleCategory->id,
            'name' => 'Published Remote Procedure',
            'slug' => 'published-remote-procedure',
            'short_description' => 'A published clinical procedure.',
            'duration_minutes' => 60,
            'price' => 700,
            'image_path' => 'https://images.example.test/published-procedure.webp',
            'is_featured' => true,
            'is_active' => true,
        ]);
        $hidden = Treatment::query()->create([
            'treatment_category_id' => $hiddenCategory->id,
            'name' => 'Hidden Homepage Procedure',
            'slug' => 'hidden-homepage-procedure',
            'short_description' => 'This must not be public.',
            'duration_minutes' => 60,
            'price' => 500,
            'is_featured' => true,
            'is_active' => true,
        ]);
        GalleryItem::query()->create([
            'treatment_id' => $hidden->id,
            'title' => 'Hidden linked result',
            'slug' => 'hidden-linked-result',
            'type' => 'before_after',
            'before_image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'after_image_path' => 'assets/web/images/hero/spa-treatment-room.jpg',
            'is_active' => true,
        ]);

        $this->get(route('web.home'))
            ->assertOk()
            ->assertSee($visible->name)
            ->assertSee('https://images.example.test/published-procedure.webp', false)
            ->assertDontSee($hidden->name)
            ->assertDontSee('Hidden linked result');
    }

    public function test_clinical_procedures_route_alias_is_available(): void
    {
        $response = $this->get('/clinical-procedures');

        $response->assertOk();
        $response->assertSee('Clinical Procedures');
    }
}
