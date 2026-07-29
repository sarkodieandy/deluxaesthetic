<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\PractitionerBlockedDate;
use App\Models\PractitionerProfile;
use App\Models\PractitionerSchedule;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\User;
use App\Services\Appointments\AvailabilityService;
use Carbon\CarbonImmutable;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PractitionerScheduleAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_manage_working_hours_and_blocked_dates(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Clinic Administrator');

        [$branch, $practitioner] = $this->fixtures();

        $this->actingAs($admin)
            ->get(route('admin.schedules.index'))
            ->assertOk()
            ->assertSee('Add working hours');

        $this->actingAs($admin)
            ->from(route('admin.schedules.index'))
            ->post(route('admin.schedules.store'), [
                'practitioner_profile_id' => $practitioner->id,
                'branch_id' => $branch->id,
                'day_of_week' => 2,
                'starts_at' => '10:00',
                'ends_at' => '16:00',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $schedule = PractitionerSchedule::query()->first();
        $this->assertNotNull($schedule);
        $this->assertSame(2, $schedule->day_of_week);

        $this->actingAs($admin)
            ->put(route('admin.schedules.update', $schedule), [
                'practitioner_profile_id' => $practitioner->id,
                'branch_id' => $branch->id,
                'day_of_week' => 3,
                'starts_at' => '11:00',
                'ends_at' => '15:00',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.schedules.index'));

        $this->assertSame(3, $schedule->fresh()->day_of_week);

        $this->actingAs($admin)
            ->post(route('admin.schedules.blocked.store'), [
                'practitioner_profile_id' => $practitioner->id,
                'starts_on' => now()->addDays(2)->toDateString(),
                'ends_on' => now()->addDays(3)->toDateString(),
                'reason' => 'Leave',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('practitioner_blocked_dates', [
            'practitioner_profile_id' => $practitioner->id,
            'reason' => 'Leave',
        ]);

        $block = PractitionerBlockedDate::query()->first();
        $this->actingAs($admin)
            ->delete(route('admin.schedules.blocked.destroy', $block))
            ->assertRedirect(route('admin.schedules.index'));

        $this->assertDatabaseMissing('practitioner_blocked_dates', ['id' => $block->id]);
    }

    public function test_blocked_dates_remove_online_slots(): void
    {
        [$branch, $practitioner] = $this->fixtures();

        $category = TreatmentCategory::create([
            'name' => 'Facial',
            'slug' => 'facial-sched',
            'is_active' => true,
        ]);
        $treatment = Treatment::create([
            'treatment_category_id' => $category->id,
            'name' => 'Facial',
            'slug' => 'facial-sched',
            'duration_minutes' => 60,
            'buffer_before_minutes' => 0,
            'buffer_after_minutes' => 0,
            'price' => 200,
            'is_active' => true,
        ]);

        $date = CarbonImmutable::now(config('clinic.timezone'))->next(CarbonImmutable::TUESDAY)->startOfDay();

        PractitionerSchedule::create([
            'practitioner_profile_id' => $practitioner->id,
            'branch_id' => $branch->id,
            'day_of_week' => $date->dayOfWeek,
            'starts_at' => '09:00',
            'ends_at' => '12:00',
            'is_active' => true,
        ]);

        $service = app(AvailabilityService::class);
        $openSlots = $service->slotsForDate($practitioner->id, $treatment->id, $branch->id, $date);
        $this->assertNotEmpty($openSlots);

        PractitionerBlockedDate::create([
            'practitioner_profile_id' => $practitioner->id,
            'starts_on' => $date->toDateString(),
            'ends_on' => $date->toDateString(),
            'reason' => 'Off',
        ]);

        $blockedSlots = $service->slotsForDate($practitioner->id, $treatment->id, $branch->id, $date);
        $this->assertCount(0, $blockedSlots);
    }

    /**
     * @return array{0: Branch, 1: PractitionerProfile}
     */
    private function fixtures(): array
    {
        $branch = Branch::create([
            'name' => 'Schedule Branch',
            'slug' => 'schedule-branch',
            'is_active' => true,
            'is_primary' => true,
        ]);

        $user = User::factory()->create(['name' => 'Dr Schedule', 'is_active' => true]);
        $user->assignRole('Practitioner');
        $practitioner = PractitionerProfile::create([
            'user_id' => $user->id,
            'slug' => 'dr-schedule',
            'is_active' => true,
        ]);

        return [$branch, $practitioner];
    }
}
