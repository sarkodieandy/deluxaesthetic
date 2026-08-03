<?php

namespace Tests\Feature;

use App\Models\CourseEnquiry;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademyEnquiryAdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_enquiry_notifies_admin_and_appears_on_dashboard(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole('Super Administrator');

        $this->post(route('web.enrol.store'), [
            'name' => 'Ama Serwaa',
            'email' => 'ama.enquiry@example.com',
            'phone' => '+233200000111',
            'preferred_channel' => 'whatsapp',
            'professional_background' => 'Beauty therapist',
            'message' => 'I would like information about the next training intake.',
            'consent' => '1',
        ])->assertRedirect(route('web.academy.student-portal.create'));

        $enquiry = CourseEnquiry::query()->where('email', 'ama.enquiry@example.com')->firstOrFail();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Latest academy enquiries')
            ->assertSee('Ama Serwaa')
            ->assertSee('Inbox (1 unread)')
            ->assertSee(route('admin.course-enquiries.show', $enquiry), false);

        $this->actingAs($admin)
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('New academy training enquiry');

        $notification = $admin->fresh()->unreadNotifications()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.notifications.read', $notification->id))
            ->assertRedirect(route('admin.course-enquiries.show', $enquiry, absolute: false));

        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Inbox (1 unread)');
    }
}
