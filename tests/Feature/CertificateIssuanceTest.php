<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Academy\CertificateIssuanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CertificateIssuanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_downloadable_certificate_for_completed_enrolment(): void
    {
        Storage::fake('public');

        Permission::create(['name' => 'certificates.issue']);
        Permission::create(['name' => 'certificates.view']);
        $role = Role::create(['name' => 'Super Administrator']);
        $role->givePermissionTo(['certificates.issue', 'certificates.view']);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole($role);

        $studentUser = User::factory()->create(['name' => 'Ama Student']);
        $studentProfileId = DB::table('student_profiles')->insertGetId([
            'user_id' => $studentUser->id,
            'student_number' => 'STU-001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId = DB::table('course_categories')->insertGetId([
            'name' => 'Aesthetics',
            'slug' => 'aesthetics',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $courseId = DB::table('courses')->insertGetId([
            'course_category_id' => $categoryId,
            'name' => 'Advanced Facial Course',
            'slug' => 'advanced-facial-course',
            'delivery_mode' => 'physical',
            'duration_hours' => 16,
            'max_students' => 12,
            'waiting_list_capacity' => 2,
            'fee' => 2500,
            'is_featured' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $scheduleId = DB::table('course_schedules')->insertGetId([
            'course_id' => $courseId,
            'starts_on' => now()->subMonth()->toDateString(),
            'ends_on' => now()->subWeek()->toDateString(),
            'capacity' => 12,
            'enrolled_count' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $enrolmentId = DB::table('enrolments')->insertGetId([
            'reference' => 'ENR-1001',
            'student_profile_id' => $studentProfileId,
            'course_id' => $courseId,
            'course_schedule_id' => $scheduleId,
            'status' => 'completed',
            'fee' => 2500,
            'amount_paid' => 2500,
            'outstanding_balance' => 0,
            'currency' => 'GHS',
            'policies_accepted' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.certificates.store'), [
            'enrolment_id' => $enrolmentId,
            'completion_date' => now()->toDateString(),
            'signatory' => 'Dr Evelyn Ejaife',
            'issue_now' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $certificate = DB::table('certificates')->first();
        $this->assertNotNull($certificate);
        $this->assertSame('issued', $certificate->status);
        $this->assertNotNull($certificate->pdf_path);
        Storage::disk('public')->assertExists($certificate->pdf_path);

        $download = $this->actingAs($admin)->get(route('admin.certificates.download', $certificate->id));
        $download->assertOk();
    }

    public function test_student_can_download_their_issued_certificate(): void
    {
        Storage::fake('public');

        Role::create(['name' => 'Student']);
        $studentUser = User::factory()->create(['name' => 'Kofi Student', 'is_active' => true]);
        $studentUser->assignRole('Student');

        $studentProfileId = DB::table('student_profiles')->insertGetId([
            'user_id' => $studentUser->id,
            'student_number' => 'STU-002',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId = DB::table('course_categories')->insertGetId([
            'name' => 'Skincare',
            'slug' => 'skincare',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $courseId = DB::table('courses')->insertGetId([
            'course_category_id' => $categoryId,
            'name' => 'Skin Analysis',
            'slug' => 'skin-analysis',
            'delivery_mode' => 'physical',
            'duration_hours' => 8,
            'max_students' => 10,
            'waiting_list_capacity' => 2,
            'fee' => 1200,
            'is_featured' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $scheduleId = DB::table('course_schedules')->insertGetId([
            'course_id' => $courseId,
            'starts_on' => now()->subMonth()->toDateString(),
            'ends_on' => now()->subWeek()->toDateString(),
            'capacity' => 10,
            'enrolled_count' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $enrolmentId = DB::table('enrolments')->insertGetId([
            'reference' => 'ENR-2002',
            'student_profile_id' => $studentProfileId,
            'course_id' => $courseId,
            'course_schedule_id' => $scheduleId,
            'status' => 'completed',
            'fee' => 1200,
            'amount_paid' => 1200,
            'outstanding_balance' => 0,
            'currency' => 'GHS',
            'policies_accepted' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $enrolment = \App\Models\Enrolment::query()->findOrFail($enrolmentId);
        $certificate = app(CertificateIssuanceService::class)->createForEnrolment($enrolment, [
            'completion_date' => now()->toDateString(),
            'signatory' => 'Dr Evelyn Ejaife',
            'issue' => true,
        ]);

        $this->actingAs($studentUser)
            ->get(route('student.certificates.download', $certificate))
            ->assertOk();
    }
}
