<?php

namespace Tests\Feature\Admin;

use App\Models\ConsultationRequest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_clinic_administrator_can_review_consultation_requests(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        $consultation = ConsultationRequest::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'preferred_channel' => 'whatsapp',
            'description' => 'Academy enrolment enquiry',
            'consent_accepted' => true,
            'status' => 'submitted',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.consultations.edit', $consultation))
            ->assertOk()
            ->assertSee('Jane Doe');
    }
}
