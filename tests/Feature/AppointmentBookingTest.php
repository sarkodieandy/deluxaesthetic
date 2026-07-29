<?php

namespace Tests\Feature;

use App\Actions\Appointments\CreateAppointmentAction;
use App\Enums\AppointmentStatus;
use App\Models\Branch;
use App\Models\ClientProfile;
use App\Models\PractitionerProfile;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_prevents_double_booking_for_the_same_practitioner_slot(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $branch = Branch::create([
            'name' => 'Test Branch',
            'slug' => 'test-branch',
            'is_active' => true,
            'is_primary' => true,
        ]);

        $clientUser = User::factory()->create();
        $clientUser->assignRole('Client');
        $client = ClientProfile::create(['user_id' => $clientUser->id, 'referral_code' => 'REFTEST1']);

        $practitionerUser = User::factory()->create();
        $practitionerUser->assignRole('Practitioner');
        $practitioner = PractitionerProfile::create([
            'user_id' => $practitionerUser->id,
            'slug' => 'test-practitioner',
            'is_active' => true,
        ]);

        $category = TreatmentCategory::create([
            'name' => 'Facial',
            'slug' => 'facial',
            'is_active' => true,
        ]);

        $treatment = Treatment::create([
            'treatment_category_id' => $category->id,
            'name' => 'Classic Facial',
            'slug' => 'classic-facial',
            'duration_minutes' => 60,
            'buffer_before_minutes' => 0,
            'buffer_after_minutes' => 15,
            'price' => 250,
            'is_active' => true,
        ]);

        $startsAt = now()->addDays(2)->setTime(10, 0);

        $action = app(CreateAppointmentAction::class);

        $first = $action->execute([
            'client_profile_id' => $client->id,
            'treatment_id' => $treatment->id,
            'practitioner_profile_id' => $practitioner->id,
            'branch_id' => $branch->id,
            'starts_at' => $startsAt,
            'status' => AppointmentStatus::Confirmed->value,
        ]);

        $this->assertNotNull($first->id);

        $this->expectException(InvalidArgumentException::class);

        $action->execute([
            'client_profile_id' => $client->id,
            'treatment_id' => $treatment->id,
            'practitioner_profile_id' => $practitioner->id,
            'branch_id' => $branch->id,
            'starts_at' => $startsAt->copy()->addMinutes(30),
            'status' => AppointmentStatus::Confirmed->value,
        ]);
    }
}
