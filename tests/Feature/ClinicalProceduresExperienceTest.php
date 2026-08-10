<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalProceduresExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalogue_is_category_led_ordered_and_only_shows_published_data(): void
    {
        $spa = $this->category('Spa Therapy', 'spa-therapy', 10);
        $injectables = $this->category('Injectable Procedures', 'injectable-treatments', 40);
        $hidden = $this->category('Hidden Category', 'hidden-category', 50, false);

        $second = $this->treatment($spa, 'Restorative Spa Procedure', 'restorative-spa-procedure', 20, 450);
        $first = $this->treatment($spa, 'Signature Spa Procedure', 'signature-spa-procedure', 5, 600, 525);
        $injectable = $this->treatment($injectables, 'Consultation Injectable Procedure', 'consultation-injectable-procedure', 10, 900);
        $this->treatment($hidden, 'Hidden Procedure', 'hidden-procedure', 1, 100);

        GalleryItem::create([
            'treatment_id' => $injectable->id,
            'title' => 'Published clinical result',
            'slug' => 'published-clinical-result',
            'type' => 'before_after',
            'before_image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'after_image_path' => 'assets/web/images/hero/spa-treatment-room.jpg',
            'is_active' => true,
        ]);

        $response = $this->get(route('web.clinical.index'));

        $response->assertOk()
            ->assertSee('Clinical pathways. One standard of care.')
            ->assertSee('Spa Therapy')
            ->assertSee('Injectable Procedures')
            ->assertSeeInOrder([$first->name, $second->name, $injectable->name])
            ->assertSee('GHS 525.00')
            ->assertSee('Published clinical result')
            ->assertDontSee('Hidden Category')
            ->assertDontSee('Hidden Procedure');

        $this->get(route('web.clinical.index', ['category' => 'spa-therapy']))
            ->assertOk()
            ->assertSee($first->name)
            ->assertSee($second->name)
            ->assertDontSee($injectable->name);
    }

    public function test_procedure_detail_shows_complete_content_booking_and_only_linked_results(): void
    {
        $category = $this->category('Injectable Procedures', 'injectable-treatments', 10);
        $treatment = $this->treatment($category, 'Detailed Clinical Procedure', 'detailed-clinical-procedure', 10, 1200, 1000, [
            'description' => 'Complete public procedure description.',
            'benefits' => ['Consultation-led plan', 'Professional aftercare'],
            'suitable_candidates' => 'Suitability is confirmed during assessment.',
            'contraindications' => 'Medical history must be reviewed.',
            'preparation_instructions' => 'Follow the preparation guidance.',
            'aftercare_instructions' => 'Follow the written aftercare plan.',
            'deposit_amount' => 250,
            'recovery_days' => 2,
        ]);
        $other = $this->treatment($category, 'Other Procedure', 'other-procedure', 20, 800);

        GalleryItem::create([
            'treatment_id' => $treatment->id,
            'title' => 'Linked procedure result',
            'slug' => 'linked-procedure-result',
            'type' => 'before_after',
            'before_image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'after_image_path' => 'assets/web/images/hero/spa-treatment-room.jpg',
            'is_active' => true,
        ]);
        GalleryItem::create([
            'treatment_id' => $other->id,
            'title' => 'Different procedure result',
            'slug' => 'different-procedure-result',
            'type' => 'before_after',
            'before_image_path' => 'assets/web/images/gallery/clinic-ambiance.jpg',
            'after_image_path' => 'assets/web/images/hero/spa-treatment-room.jpg',
            'is_active' => true,
        ]);

        $this->get(route('web.treatments.show', $treatment->slug))
            ->assertOk()
            ->assertSee('Complete public procedure description.')
            ->assertSee('GHS 1,000.00')
            ->assertSee('Suitability is confirmed during assessment.')
            ->assertSee('Medical history must be reviewed.')
            ->assertSee('Linked procedure result')
            ->assertDontSee('Different procedure result')
            ->assertSee(route('web.booking.create', ['treatment_id' => $treatment->id]), false);
    }

    public function test_hidden_category_hides_its_procedure_detail(): void
    {
        $category = $this->category('Hidden', 'hidden', 10, false);
        $treatment = $this->treatment($category, 'Hidden Procedure', 'hidden-procedure', 10, 500);

        $this->get(route('web.treatments.show', $treatment->slug))->assertNotFound();
    }

    private function category(string $name, string $slug, int $order, bool $active = true): TreatmentCategory
    {
        return TreatmentCategory::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $name.' description.',
            'image_path' => 'assets/web/images/hero/hero-botox.webp',
            'sort_order' => $order,
            'is_active' => $active,
        ]);
    }

    /** @param array<string, mixed> $overrides */
    private function treatment(
        TreatmentCategory $category,
        string $name,
        string $slug,
        int $order,
        float $price,
        ?float $promotionalPrice = null,
        array $overrides = [],
    ): Treatment {
        return Treatment::create($overrides + [
            'treatment_category_id' => $category->id,
            'name' => $name,
            'slug' => $slug,
            'short_description' => $name.' summary.',
            'duration_minutes' => 60,
            'price' => $price,
            'promotional_price' => $promotionalPrice,
            'sort_order' => $order,
            'is_active' => true,
        ]);
    }
}
