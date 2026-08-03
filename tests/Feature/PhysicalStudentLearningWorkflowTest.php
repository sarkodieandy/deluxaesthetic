<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSchedule;
use App\Models\CourseSession;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\StudentSupportRequest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PhysicalStudentLearningWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_physical_student_can_use_course_calendar_assignment_and_support_workflows(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        [$student, $course, $schedule, $enrolment] = $this->activeStudentEnrolment();
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Super Administrator');

        CourseSession::create([
            'course_schedule_id' => $schedule->id, 'session_date' => now()->addDay()->toDateString(),
            'starts_at' => '09:00', 'ends_at' => '12:00', 'topic' => 'Practical techniques', 'status' => 'scheduled',
        ]);

        $this->actingAs($student)->get(route('student.course.show'))->assertOk()->assertSee($course->name);
        $this->actingAs($student)->get(route('student.calendar.index'))->assertOk()->assertSee('Practical techniques');

        $this->actingAs($admin)->post(route('admin.assignments.store'), [
            'course_id' => $course->id, 'title' => 'Clinical worksheet', 'instructions' => 'Complete the attached worksheet.',
            'due_at' => now()->addWeek()->format('Y-m-d H:i:s'),
            'attachment' => UploadedFile::fake()->create('worksheet.pdf', 100, 'application/pdf'),
            'allow_resubmission' => '1',
        ])->assertRedirect();
        $assignment = Assignment::firstOrFail();

        $this->actingAs($student)->get(route('student.assignments.index'))->assertOk()->assertSee('Clinical worksheet');
        $this->actingAs($student)->get(route('student.assignments.download', $assignment))->assertOk();
        $this->actingAs($student)->post(route('student.assignments.submit', $assignment), [
            'notes' => 'My completed work', 'file' => UploadedFile::fake()->create('answer.pdf', 80, 'application/pdf'),
        ])->assertRedirect();
        $submission = $assignment->submissions()->firstOrFail();

        $this->actingAs($admin)->put(route('admin.assignment-submissions.review', $submission), [
            'score' => 90, 'feedback' => 'Excellent practical understanding.',
        ])->assertRedirect();
        $this->actingAs($student)->get(route('student.assignments.show', $assignment))
            ->assertOk()->assertSee('Excellent practical understanding.');

        $this->actingAs($student)->post(route('student.support.store'), [
            'category' => 'course_question', 'subject' => 'Class materials', 'message' => 'Please confirm the items to bring.',
        ])->assertRedirect();
        $support = StudentSupportRequest::firstOrFail();
        $this->actingAs($admin)->put(route('admin.student-support.update', $support), [
            'status' => 'resolved', 'admin_response' => 'Please bring your student kit and notebook.',
        ])->assertRedirect();
        $this->actingAs($student)->get(route('student.support.index'))
            ->assertOk()->assertSee('Please bring your student kit and notebook.');
    }

    private function activeStudentEnrolment(): array
    {
        $student = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $student->assignRole(Role::findOrCreate('Student'));
        $profile = StudentProfile::create(['user_id' => $student->id, 'student_number' => 'STU-FLOW-1', 'profile_completed_at' => now()]);
        $category = CourseCategory::create(['name' => 'Clinical', 'slug' => 'clinical', 'is_active' => true]);
        $course = Course::create(['course_category_id' => $category->id, 'name' => 'Physical Botox Training', 'slug' => 'physical-botox-training', 'fee' => 1500, 'is_active' => true]);
        $schedule = CourseSchedule::create(['course_id' => $course->id, 'starts_on' => now(), 'ends_on' => now()->addMonth(), 'capacity' => 10, 'is_active' => true]);
        $enrolment = Enrolment::create([
            'reference' => 'ENR-FLOW-1', 'student_profile_id' => $profile->id, 'course_id' => $course->id,
            'course_schedule_id' => $schedule->id, 'status' => 'active', 'fee' => 1500, 'amount_paid' => 1500,
            'outstanding_balance' => 0, 'policies_accepted' => true, 'activated_at' => now(),
        ]);

        return [$student, $course, $schedule, $enrolment];
    }
}
