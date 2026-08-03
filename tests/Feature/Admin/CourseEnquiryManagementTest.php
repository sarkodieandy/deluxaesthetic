<?php

namespace Tests\Feature\Admin;

use App\Models\CourseEnquiry;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseEnquiryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $this->admin->assignRole('Super Administrator');
    }

    public function test_admin_can_review_edit_and_delete_enquiry(): void
    {
        $enquiry = CourseEnquiry::create([
            'full_name' => 'Adwoa Mensah',
            'email' => 'adwoa@example.com',
            'phone' => '+233200000222',
            'preferred_contact_method' => 'whatsapp',
            'message' => 'I am interested in the next physical class.',
            'privacy_consent' => true,
            'status' => 'submitted',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.course-enquiries.show', $enquiry))
            ->assertOk()
            ->assertSee('Save review')
            ->assertSee('Open WhatsApp')
            ->assertSee('Delete enquiry');

        $this->actingAs($this->admin)->put(route('admin.course-enquiries.update', $enquiry), [
            'full_name' => 'Adwoa A. Mensah',
            'email' => 'adwoa@example.com',
            'phone' => '+233200000222',
            'preferred_contact_method' => 'phone',
            'professional_background' => 'Beauty therapist',
            'message' => 'Interested in physical training.',
            'status' => 'contacted',
            'assigned_to' => $this->admin->id,
            'internal_notes' => 'Called applicant and discussed the next intake.',
        ])->assertRedirect();

        $this->assertDatabaseHas('course_enquiries', [
            'id' => $enquiry->id,
            'full_name' => 'Adwoa A. Mensah',
            'status' => 'contacted',
            'assigned_to' => $this->admin->id,
            'internal_notes' => 'Called applicant and discussed the next intake.',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.course-enquiries.index', ['status' => 'contacted', 'q' => 'Adwoa']))
            ->assertOk()
            ->assertSee('Adwoa A. Mensah')
            ->assertSee('Applicant contacted');

        $this->actingAs($this->admin)
            ->delete(route('admin.course-enquiries.destroy', $enquiry))
            ->assertRedirect(route('admin.course-enquiries.index'));

        $this->assertSoftDeleted($enquiry);
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_view_only_staff_cannot_edit_or_delete_enquiry(): void
    {
        $receptionist = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $receptionist->assignRole('Receptionist');
        $enquiry = CourseEnquiry::create([
            'full_name' => 'Test Applicant', 'email' => 'test@example.com', 'phone' => '0200000000',
            'preferred_contact_method' => 'email', 'privacy_consent' => true, 'status' => 'submitted',
        ]);

        $this->actingAs($receptionist)->get(route('admin.course-enquiries.show', $enquiry))->assertOk()->assertDontSee('Delete enquiry');
        $this->actingAs($receptionist)->put(route('admin.course-enquiries.update', $enquiry), [])->assertForbidden();
        $this->actingAs($receptionist)->delete(route('admin.course-enquiries.destroy', $enquiry))->assertForbidden();
    }
}
