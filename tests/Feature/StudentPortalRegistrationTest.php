<?php

namespace Tests\Feature;

use App\Jobs\Emails\SendTemplatedEmail;
use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentPortalRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_academy_nav_opens_student_portal_registration(): void
    {
        $this->get(route('web.academy.index'))
            ->assertOk()
            ->assertSee(route('web.academy.student-portal.create'))
            ->assertSee('Apply as a student')
            ->assertSee('Student login');

        $this->get(route('web.academy.student-portal.create'))
            ->assertOk()
            ->assertSee('Student application')
            ->assertSee('Approved student login');
    }

    public function test_public_enrolment_links_use_the_pending_student_application(): void
    {
        $applicationUrl = route('web.academy.student-portal.create');

        $this->get(route('web.academy.index'))->assertSee($applicationUrl, false);
        $this->get(route('web.contact'))->assertSee($applicationUrl, false);
    }

    public function test_legacy_enrolment_page_redirects_to_student_application(): void
    {
        $this->get(route('web.enrol', ['course' => 42]))
            ->assertRedirect(route('web.academy.student-portal.create', ['course' => 42]));
    }

    public function test_guest_application_is_pending_and_cannot_login_before_approval(): void
    {
        Role::findOrCreate('Student');

        $response = $this->post(route('web.academy.student-portal.store'), [
            'name' => 'Ama Mensah',
            'email' => 'ama.student@example.com',
            'phone' => '+233200000099',
            'message' => 'I would like to study advanced skin treatments at the academy.',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'privacy_consent' => '1',
        ]);

        $response->assertRedirect(route('web.academy.student-portal.create'));

        $user = User::query()->where('email', 'ama.student@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Student'));
        $this->assertNotNull($user->studentProfile);
        $this->assertNotNull($user->studentProfile->profile_completed_at);
        $this->assertFalse($user->is_active);
        $this->assertNull($user->studentProfile->portal_activated_at);
        $this->assertDatabaseHas('course_enquiries', [
            'user_id' => $user->id,
            'email' => 'ama.student@example.com',
            'status' => 'submitted',
        ]);
        $this->assertGuest();

        $this->post(route('login'), [
            'email' => 'ama.student@example.com',
            'password' => 'Password1!',
        ])->assertSessionHasErrors([
            'email' => 'Your academy application is awaiting approval. Our admissions team will contact you before portal access is activated.',
        ]);
        $this->assertGuest();
    }

    public function test_registration_normalizes_email_and_notifies_admin(): void
    {
        Queue::fake([SendTemplatedEmail::class]);
        $this->seed(RolePermissionSeeder::class);
        $this->seed(EmailTemplateSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Super Administrator');

        $this->post(route('web.academy.student-portal.store'), [
            'name' => '  Akosua Boateng  ',
            'email' => 'AKOSUA.STUDENT@EXAMPLE.COM',
            'phone' => ' +233200000100 ',
            'message' => 'I want to study Botox and facial assessment in person.',
            'password' => 'student123',
            'password_confirmation' => 'student123',
            'privacy_consent' => '1',
        ])->assertRedirect(route('web.academy.student-portal.create'));

        $student = User::query()->where('email', 'akosua.student@example.com')->firstOrFail();
        $this->assertSame('Akosua Boateng', $student->name);
        $this->assertTrue($student->hasRole('Student'));
        $this->assertFalse($student->is_active);
        $this->assertFalse($student->hasAnyRole(config('admin.roles')));
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);
        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'akosua.student@example.com',
            'template_key' => 'academy.application_received',
            'status' => 'queued',
        ]);
        Queue::assertPushed(SendTemplatedEmail::class);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Student applications awaiting approval')
            ->assertSee('Akosua Boateng');

        $this->actingAs($admin)
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee('Akosua Boateng')
            ->assertSee('Awaiting approval');

        $this->actingAs($admin)
            ->post(route('admin.students.approve', $student), ['contact_confirmed' => '1'])
            ->assertRedirect();

        $student->refresh();
        $this->assertTrue($student->is_active);
        $this->assertNotNull($student->studentProfile->fresh()->portal_activated_at);

        $this->post(route('logout'));
        $this->post(route('login'), [
            'email' => 'akosua.student@example.com',
            'password' => 'student123',
        ])->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_admin_can_delete_unapproved_student_application(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Super Administrator');

        $this->post(route('web.academy.student-portal.store'), [
            'name' => 'Pending Applicant',
            'email' => 'pending.student@example.com',
            'phone' => '+233200000101',
            'message' => 'I am interested in joining the next physical aesthetics class.',
            'password' => 'student123',
            'password_confirmation' => 'student123',
            'privacy_consent' => '1',
        ])->assertRedirect(route('web.academy.student-portal.create'));

        $student = User::query()->where('email', 'pending.student@example.com')->firstOrFail();
        $this->actingAs($admin)->delete(route('admin.students.destroy', $student))->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $student->id]);
        $this->assertDatabaseMissing('course_enquiries', ['user_id' => $student->id]);
    }

    public function test_logged_in_student_is_redirected_from_academy_to_dashboard(): void
    {
        $student = User::factory()->create([
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $student->assignRole(Role::findOrCreate('Student'));
        StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-2026-0001',
        ]);

        $this->actingAs($student)
            ->get(route('web.academy.index'))
            ->assertRedirect(route('student.dashboard'));
    }
}
