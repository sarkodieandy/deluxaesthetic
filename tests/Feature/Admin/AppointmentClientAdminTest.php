<?php

namespace Tests\Feature\Admin;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ClientProfile;
use App\Models\PractitionerProfile;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentClientAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_receptionist_can_open_appointments_and_clients(): void
    {
        $staff = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $staff->assignRole('Receptionist');

        $clientUser = User::factory()->create();
        $clientUser->assignRole('Client');
        $client = ClientProfile::create(['user_id' => $clientUser->id]);

        $branch = Branch::create(['name' => 'Main', 'slug' => 'main', 'is_active' => true, 'is_primary' => true]);
        $category = TreatmentCategory::create(['name' => 'Facials', 'slug' => 'facials', 'is_active' => true]);
        $treatment = Treatment::create(['treatment_category_id' => $category->id, 'name' => 'Glow', 'slug' => 'glow', 'duration_minutes' => 60, 'price' => 100, 'is_active' => true]);
        $practitionerUser = User::factory()->create();
        $practitionerUser->assignRole('Practitioner');
        $practitioner = PractitionerProfile::create(['user_id' => $practitionerUser->id, 'slug' => 'prac', 'is_active' => true]);

        $appointment = Appointment::create([
            'reference' => 'APT-1001',
            'client_profile_id' => $client->id,
            'treatment_id' => $treatment->id,
            'practitioner_profile_id' => $practitioner->id,
            'branch_id' => $branch->id,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
            'status' => AppointmentStatus::Pending,
            'price' => 100,
            'deposit_amount' => 0,
            'amount_paid' => 0,
            'currency' => 'GHS',
        ]);

        $this->actingAs($staff)->get(route('admin.appointments.edit', $appointment))->assertOk();
        $this->actingAs($staff)->get(route('admin.clients.show', $client))->assertOk()->assertSee((string) $clientUser->email);
    }
}
