<?php

namespace Tests\Feature;

use App\Events\EnrolmentActivated;
use App\Models\User;
use App\Notifications\PortalAlertNotification;
use App\Services\Notifications\InAppNotificationService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PortalInAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_student_can_view_and_mark_notifications_read(): void
    {
        $student = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $student->assignRole(Role::findOrCreate('Student'));
        \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-NOTIF-1',
            'profile_completed_at' => now(),
        ]);

        app(InAppNotificationService::class)->notifyUser($student, [
            'title' => 'Test alert',
            'message' => 'Something happened in your portal.',
            'action_url' => route('student.dashboard', absolute: false),
            'category' => 'test',
        ]);

        $notification = $student->fresh()->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        $this->actingAs($student)
            ->get(route('student.notifications.index'))
            ->assertOk()
            ->assertSee('Test alert');

        $this->actingAs($student)
            ->post(route('student.notifications.read', $notification->id))
            ->assertRedirect(route('student.dashboard'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_admin_inbox_lists_notifications(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole(Role::findOrCreate('Super Administrator'));

        app(InAppNotificationService::class)->notifyUser($admin, [
            'title' => 'Admin alert',
            'message' => 'A student needs help.',
            'category' => 'support',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('Admin alert');
    }

    public function test_support_request_notifies_student_and_admins(): void
    {
        Notification::fake();

        $student = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $student->assignRole(Role::findOrCreate('Student'));
        \App\Models\StudentProfile::query()->create([
            'user_id' => $student->id,
            'student_number' => 'STU-NOTIF-2',
            'profile_completed_at' => now(),
        ]);

        $admin = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $admin->assignRole(Role::findOrCreate('Super Administrator'));

        $this->actingAs($student)->post(route('student.support.store'), [
            'category' => 'course_question',
            'subject' => 'Need help with materials',
            'message' => 'I cannot download week 1 notes.',
        ])->assertRedirect();

        Notification::assertSentTo($student, PortalAlertNotification::class);
        Notification::assertSentTo($admin, PortalAlertNotification::class);
    }
}
