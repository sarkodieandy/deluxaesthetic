<?php

namespace Tests\Feature\Admin;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ClientProfile;
use App\Models\PractitionerProfile;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\User;
use Database\Seeders\ClinicalCategorySeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClinicalProcedureManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_clinic_admin_can_manage_categories_and_complete_procedure_details(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $this->actingAs($admin)->post(route('admin.treatment-categories.store'), [
            'name' => 'Injectable Procedures',
            'description' => 'Consultation-led injectable care.',
            'image_url' => 'https://images.example.test/injectables.webp',
            'sort_order' => 40,
            'is_active' => '1',
        ])->assertRedirect(route('admin.treatment-categories.index'));

        $category = TreatmentCategory::query()->where('slug', 'injectable-procedures')->firstOrFail();
        $this->assertSame('https://images.example.test/injectables.webp', $category->image_path);

        $practitionerUser = User::factory()->create(['is_active' => true]);
        $practitionerUser->assignRole('Practitioner');
        $practitioner = PractitionerProfile::create([
            'user_id' => $practitionerUser->id,
            'slug' => 'clinical-specialist',
            'professional_title' => 'Aesthetic Practitioner',
            'is_active' => true,
        ]);

        $image = UploadedFile::fake()->image('procedure.webp', 1200, 900);
        $this->actingAs($admin)->post(route('admin.treatments.store'), [
            'treatment_category_id' => $category->id,
            'name' => 'Precision Consultation Procedure',
            'short_description' => 'A consultation-led clinical procedure.',
            'description' => 'A detailed, professionally assessed procedure.',
            'duration_minutes' => 75,
            'recovery_days' => 2,
            'price' => 950,
            'promotional_price' => 850,
            'deposit_amount' => 200,
            'recommended_sessions' => 2,
            'buffer_before_minutes' => 10,
            'buffer_after_minutes' => 20,
            'sort_order' => 5,
            'benefits' => "Personal assessment\nWritten aftercare",
            'suitable_candidates' => 'Confirmed after consultation.',
            'contraindications' => 'Medical history is reviewed before treatment.',
            'preparation_instructions' => 'Attend the pre-treatment consultation.',
            'aftercare_instructions' => 'Follow the practitioner plan.',
            'seo_title' => 'Precision consultation procedure in Accra',
            'seo_description' => 'Learn about this consultation-led clinical procedure.',
            'practitioner_profile_ids' => [$practitioner->id],
            'image' => $image,
            'is_featured' => '1',
            'is_active' => '1',
        ])->assertRedirect(route('admin.treatments.index'));

        $treatment = Treatment::query()->where('slug', 'precision-consultation-procedure')->firstOrFail();
        $this->assertSame(5, $treatment->sort_order);
        $this->assertSame('850.00', $treatment->promotional_price);
        $this->assertSame(['Personal assessment', 'Written aftercare'], $treatment->benefits);
        $this->assertTrue($treatment->practitioners()->whereKey($practitioner->id)->exists());
        Storage::disk('public')->assertExists($treatment->image_path);

        $storedImage = $treatment->image_path;
        $this->actingAs($admin)->put(route('admin.treatments.update', $treatment), [
            'treatment_category_id' => $category->id,
            'name' => $treatment->name,
            'short_description' => $treatment->short_description,
            'duration_minutes' => 60,
            'price' => 950,
            'sort_order' => 7,
            'remove_image' => '1',
            'practitioner_profile_ids' => [$practitioner->id],
            'is_active' => '1',
        ])->assertRedirect(route('admin.treatments.index'));

        $treatment->refresh();
        $this->assertNull($treatment->image_path);
        $this->assertSame(7, $treatment->sort_order);
        Storage::disk('public')->assertMissing($storedImage);

        $this->actingAs($admin)
            ->delete(route('admin.treatment-categories.destroy', $category))
            ->assertSessionHasErrors('category');
    }

    public function test_treatment_routes_enforce_granular_write_permissions(): void
    {
        $viewer = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $viewer->assignRole('Content Manager');
        $viewer->givePermissionTo('treatments.view');

        $this->actingAs($viewer)->get(route('admin.treatments.index'))->assertOk();
        $this->actingAs($viewer)->get(route('admin.treatment-categories.index'))->assertOk();
        $this->actingAs($viewer)->get(route('admin.treatments.create'))->assertForbidden();
        $this->actingAs($viewer)->post(route('admin.treatment-categories.store'), [
            'name' => 'Unauthorised',
            'sort_order' => 1,
        ])->assertForbidden();
    }

    public function test_confirmed_category_seeder_does_not_overwrite_admin_changes(): void
    {
        $this->seed(ClinicalCategorySeeder::class);

        $category = TreatmentCategory::query()->where('slug', 'spa-therapy')->firstOrFail();
        $category->update(['description' => 'Administrator-approved wording.', 'is_active' => false]);

        $this->seed(ClinicalCategorySeeder::class);

        $category->refresh();
        $this->assertSame('Administrator-approved wording.', $category->description);
        $this->assertFalse($category->is_active);
        $this->assertDatabaseCount('treatment_categories', 4);
    }

    public function test_procedure_with_appointment_history_must_be_unpublished_instead_of_deleted(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');
        $category = TreatmentCategory::query()->create([
            'name' => 'Appointment category',
            'slug' => 'appointment-category',
            'is_active' => true,
        ]);
        $treatment = Treatment::query()->create([
            'treatment_category_id' => $category->id,
            'name' => 'History-safe treatment',
            'slug' => 'history-safe-treatment',
            'short_description' => 'A treatment with an appointment.',
            'duration_minutes' => 60,
            'price' => 500,
            'is_active' => true,
        ]);
        $branch = Branch::query()->create([
            'name' => 'History Branch',
            'slug' => 'history-branch',
            'is_active' => true,
            'is_primary' => true,
        ]);
        $clientUser = User::factory()->create();
        $client = ClientProfile::query()->create([
            'user_id' => $clientUser->id,
            'referral_code' => 'HISTORY01',
        ]);
        $practitionerUser = User::factory()->create(['is_active' => true]);
        $practitioner = PractitionerProfile::query()->create([
            'user_id' => $practitionerUser->id,
            'slug' => 'history-practitioner',
            'is_active' => true,
        ]);
        $appointment = Appointment::query()->create([
            'reference' => 'APT-HISTORY-001',
            'client_profile_id' => $client->id,
            'treatment_id' => $treatment->id,
            'practitioner_profile_id' => $practitioner->id,
            'branch_id' => $branch->id,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
            'status' => 'confirmed',
            'price' => 500,
            'deposit_amount' => 0,
            'amount_paid' => 0,
            'currency' => 'GHS',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.treatments.destroy', $treatment))
            ->assertSessionHasErrors('treatment');

        $this->assertNull($treatment->fresh()->deleted_at);

        $treatment->delete();
        $this->assertSame('History-safe treatment', $appointment->fresh()->treatment?->name);
    }
}
