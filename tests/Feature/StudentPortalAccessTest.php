<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentPortalAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_student_dashboard(): void
    {
        $this->get(route('student.dashboard'))->assertRedirect(route('login'));
    }

    public function test_client_cannot_access_student_dashboard(): void
    {
        $client = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $client->assignRole(Role::findOrCreate('Client'));

        $this->actingAs($client)->get(route('student.dashboard'))->assertForbidden();
    }

    public function test_student_without_active_enrolment_sees_pending_state(): void
    {
        $student = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole(Role::findOrCreate('Student'));
        \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-1001',
            'profile_completed_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Portal pending activation');
    }

    public function test_student_materials_without_enrolment_shows_friendly_page(): void
    {
        $student = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole(Role::findOrCreate('Student'));
        \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-1002',
            'profile_completed_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.materials.index'))
            ->assertOk()
            ->assertSee(__('student.portal.no_enrolment_title'));
    }
}
