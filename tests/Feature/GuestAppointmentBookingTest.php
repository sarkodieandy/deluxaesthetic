<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ClientProfile;
use App\Models\PractitionerProfile;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestAppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_book_without_creating_an_account(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        [$branch, $practitioner, $treatment] = $this->bookingFixtures();

        $startsAt = now()->addDays(3)->setTime(11, 0)->format('Y-m-d\TH:i');

        $response = $this->post(route('web.booking.store'), [
            'guest_name' => 'Ama Guest',
            'guest_email' => 'ama.guest@example.com',
            'guest_phone' => '+233200000001',
            'treatment_id' => $treatment->id,
            'practitioner_profile_id' => $practitioner->id,
            'branch_id' => $branch->id,
            'starts_at' => $startsAt,
            'goals' => 'Brightening facial consultation',
            'client_notes' => 'First visit',
            'consent' => '1',
        ]);

        $appointment = Appointment::query()->first();

        $this->assertNotNull($appointment);
        $response->assertRedirect(route('web.booking.confirmation', $appointment->reference));

        $this->assertDatabaseHas('client_profiles', [
            'guest_email' => 'ama.guest@example.com',
            'guest_name' => 'Ama Guest',
            'user_id' => null,
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'ama.guest@example.com',
        ]);

        $this->assertSame(AppointmentStatus::AwaitingPayment, $appointment->status);
        $this->assertTrue($appointment->clientProfile->isGuest());
    }

    public function test_booking_page_is_available_to_guests(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->get(route('web.booking.create'))
            ->assertOk()
            ->assertSee('without creating an account', false)
            ->assertSee('name="guest_name"', false);
    }

    public function test_authenticated_client_still_books_into_their_profile(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        [$branch, $practitioner, $treatment] = $this->bookingFixtures();

        $clientUser = User::factory()->create([
            'email' => 'client.book@example.com',
        ]);
        $clientUser->assignRole('Client');
        $client = ClientProfile::create([
            'user_id' => $clientUser->id,
            'referral_code' => 'CLIENT01',
        ]);

        $startsAt = now()->addDays(4)->setTime(14, 0)->format('Y-m-d\TH:i');

        $this->actingAs($clientUser)
            ->post(route('web.booking.store'), [
                'treatment_id' => $treatment->id,
                'practitioner_profile_id' => $practitioner->id,
                'branch_id' => $branch->id,
                'starts_at' => $startsAt,
                'goals' => 'Follow-up treatment',
                'consent' => '1',
            ])
            ->assertRedirect(route('web.booking.confirmation', Appointment::query()->latest('id')->value('reference')));

        $this->assertDatabaseHas('appointments', [
            'client_profile_id' => $client->id,
        ]);
        $this->assertEquals(0, ClientProfile::query()->whereNull('user_id')->count());
        $this->assertEquals(1, ClientProfile::query()->where('user_id', $clientUser->id)->count());
    }

    /**
     * @return array{0: Branch, 1: PractitionerProfile, 2: Treatment}
     */
    private function bookingFixtures(): array
    {
        $branch = Branch::create([
            'name' => 'Airport Branch',
            'slug' => 'airport-branch',
            'is_active' => true,
            'is_primary' => true,
        ]);

        $practitionerUser = User::factory()->create();
        $practitionerUser->assignRole('Practitioner');
        $practitioner = PractitionerProfile::create([
            'user_id' => $practitionerUser->id,
            'slug' => 'guest-booking-practitioner',
            'professional_title' => 'Aesthetician',
            'is_active' => true,
        ]);

        $category = TreatmentCategory::create([
            'name' => 'Facial',
            'slug' => 'facial-guest',
            'is_active' => true,
        ]);

        $treatment = Treatment::create([
            'treatment_category_id' => $category->id,
            'name' => 'Glow Facial',
            'slug' => 'glow-facial',
            'duration_minutes' => 60,
            'buffer_before_minutes' => 0,
            'buffer_after_minutes' => 15,
            'price' => 350,
            'is_active' => true,
        ]);

        return [$branch, $practitioner, $treatment];
    }
}
