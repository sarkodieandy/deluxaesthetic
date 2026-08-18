<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseMaterial;
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
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PhysicalStudentLearningWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_physical_student_can_use_course_calendar_assignment_and_support_workflows(): void
    {
        Storage::fake('academy_private');
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        [$student, $course, $schedule, $enrolment] = $this->activeStudentEnrolment();
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Super Administrator');

        $this->actingAs($admin)->post(route('admin.course-schedules.sessions.store', $schedule), [
            'session_date' => now()->addDay()->toDateString(),
            'starts_at' => '09:00', 'ends_at' => '12:00', 'topic' => 'Practical techniques', 'status' => 'scheduled',
        ])->assertRedirect();

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $student->id]);

        $this->actingAs($student)->get(route('student.course.show'))->assertOk()->assertSee($course->name);
        $this->actingAs($student)->get(route('student.calendar.index'))->assertOk()->assertSee('Practical techniques');

        $this->actingAs($admin)->post(route('admin.assignments.store'), [
            'course_id' => $course->id, 'title' => 'Clinical worksheet', 'instructions' => 'Complete the attached worksheet.',
            'enrolment_id' => $enrolment->id,
            'due_at' => now()->addWeek()->format('Y-m-d H:i:s'),
            'attachment' => UploadedFile::fake()->create('worksheet.pdf', 100, 'application/pdf'),
            'allow_resubmission' => '1',
        ])->assertRedirect();
        $assignment = Assignment::firstOrFail();
        Storage::disk('academy_private')->assertExists($assignment->attachment_path);
        Storage::disk('public')->assertMissing($assignment->attachment_path);
        $this->assertGreaterThanOrEqual(2, $student->notifications()->count());

        $this->actingAs($student)->get(route('student.assignments.index'))->assertOk()->assertSee('Clinical worksheet');
        $this->actingAs($student)->get(route('student.assignments.download', $assignment))->assertOk();
        $this->actingAs($student)->post(route('student.assignments.submit', $assignment), [
            'notes' => 'My completed work', 'file' => UploadedFile::fake()->create('answer.pdf', 80, 'application/pdf'),
        ])->assertRedirect();
        $submission = $assignment->submissions()->firstOrFail();
        Storage::disk('academy_private')->assertExists($submission->file_path);
        Storage::disk('public')->assertMissing($submission->file_path);

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

    public function test_student_with_multiple_active_enrolments_sees_only_their_courses_and_learning_resources(): void
    {
        Storage::fake('academy_private');
        $this->seed(RolePermissionSeeder::class);

        [$student, $firstCourse, $firstSchedule, $firstEnrolment] = $this->activeStudentEnrolment();
        $profile = $student->studentProfile;
        $category = $firstCourse->category;

        $secondCourse = Course::query()->create([
            'course_category_id' => $category->id,
            'name' => 'Advanced Fillers Training',
            'slug' => 'advanced-fillers-training',
            'fee' => 2400,
            'is_active' => true,
        ]);
        $secondSchedule = CourseSchedule::query()->create([
            'course_id' => $secondCourse->id,
            'starts_on' => now(),
            'ends_on' => now()->addMonth(),
            'capacity' => 10,
            'is_active' => true,
        ]);
        $secondEnrolment = Enrolment::query()->create([
            'reference' => 'ENR-FLOW-2',
            'student_profile_id' => $profile->id,
            'course_id' => $secondCourse->id,
            'course_schedule_id' => $secondSchedule->id,
            'status' => 'active',
            'fee' => 2400,
            'amount_paid' => 2400,
            'outstanding_balance' => 0,
            'policies_accepted' => true,
            'activated_at' => now()->addMinute(),
        ]);

        $otherStudent = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        $otherStudent->assignRole(Role::findOrCreate('Student'));
        $otherProfile = StudentProfile::query()->create([
            'user_id' => $otherStudent->id,
            'student_number' => 'STU-FLOW-OTHER',
            'profile_completed_at' => now(),
        ]);
        $otherSchedule = CourseSchedule::query()->create([
            'course_id' => $secondCourse->id,
            'starts_on' => now(),
            'ends_on' => now()->addMonth(),
            'capacity' => 10,
            'is_active' => true,
        ]);
        $otherEnrolment = Enrolment::query()->create([
            'reference' => 'ENR-FLOW-OTHER',
            'student_profile_id' => $otherProfile->id,
            'course_id' => $secondCourse->id,
            'course_schedule_id' => $otherSchedule->id,
            'status' => 'active',
            'fee' => 2400,
            'amount_paid' => 2400,
            'outstanding_balance' => 0,
            'policies_accepted' => true,
            'activated_at' => now(),
        ]);

        $firstMaterial = $this->material($firstCourse, $firstEnrolment, 'Owned Botox Handbook', 'materials/owned-botox.pdf');
        $secondMaterial = $this->material($secondCourse, $secondEnrolment, 'Owned Fillers Handbook', 'materials/owned-fillers.pdf');
        $foreignMaterial = $this->material($secondCourse, $otherEnrolment, 'Other Student Private Handbook', 'materials/other-student.pdf');
        foreach ([$firstMaterial, $secondMaterial, $foreignMaterial] as $material) {
            Storage::disk('academy_private')->put($material->file_path, 'private material');
        }

        $firstAssignment = Assignment::query()->create([
            'course_id' => $firstCourse->id,
            'enrolment_id' => $firstEnrolment->id,
            'title' => 'Owned Botox Assignment',
        ]);
        $secondAssignment = Assignment::query()->create([
            'course_id' => $secondCourse->id,
            'enrolment_id' => $secondEnrolment->id,
            'title' => 'Owned Fillers Assignment',
        ]);
        $foreignAssignment = Assignment::query()->create([
            'course_id' => $secondCourse->id,
            'enrolment_id' => $otherEnrolment->id,
            'title' => 'Other Student Private Assignment',
        ]);

        CourseSession::query()->create([
            'course_schedule_id' => $firstSchedule->id,
            'session_date' => now()->addDay(),
            'starts_at' => '09:00',
            'ends_at' => '11:00',
            'topic' => 'Botox practical session',
            'status' => 'scheduled',
        ]);
        CourseSession::query()->create([
            'course_schedule_id' => $secondSchedule->id,
            'session_date' => now()->addDays(2),
            'starts_at' => '10:00',
            'ends_at' => '12:00',
            'topic' => 'Fillers practical session',
            'status' => 'scheduled',
        ]);
        CourseSession::query()->create([
            'course_schedule_id' => $otherSchedule->id,
            'session_date' => now()->addDays(3),
            'starts_at' => '13:00',
            'ends_at' => '15:00',
            'topic' => 'Other Student Private Session',
            'status' => 'scheduled',
        ]);

        $this->actingAs($student)->get(route('student.course.show'))
            ->assertOk()
            ->assertSee($firstCourse->name)
            ->assertSee($secondCourse->name);

        $this->actingAs($student)->get(route('student.materials.index'))
            ->assertOk()
            ->assertSee('Owned Botox Handbook')
            ->assertSee('Owned Fillers Handbook')
            ->assertDontSee('Other Student Private Handbook');
        $this->actingAs($student)->get(route('student.materials.download', $secondMaterial))->assertOk();
        $this->actingAs($student)->get(route('student.materials.download', $foreignMaterial))->assertForbidden();

        $this->actingAs($student)->get(route('student.assignments.index'))
            ->assertOk()
            ->assertSee('Owned Botox Assignment')
            ->assertSee('Owned Fillers Assignment')
            ->assertDontSee('Other Student Private Assignment');
        $this->actingAs($student)->get(route('student.assignments.show', $secondAssignment))->assertOk();
        $this->actingAs($student)->get(route('student.assignments.show', $foreignAssignment))->assertForbidden();

        $this->actingAs($student)->get(route('student.calendar.index'))
            ->assertOk()
            ->assertSee($firstCourse->name)
            ->assertSee($secondCourse->name)
            ->assertSee('Botox practical session')
            ->assertSee('Fillers practical session')
            ->assertDontSee('Other Student Private Session');

        AttendanceRecord::query()->create([
            'enrolment_id' => $secondEnrolment->id,
            'course_schedule_id' => $secondSchedule->id,
            'session_date' => now()->toDateString(),
            'status' => 'present',
        ]);
        $assessment = Assessment::query()->create([
            'course_id' => $secondCourse->id,
            'title' => 'Advanced fillers practical',
            'max_score' => 100,
            'passing_score' => 60,
        ]);
        AssessmentResult::query()->create([
            'assessment_id' => $assessment->id,
            'enrolment_id' => $secondEnrolment->id,
            'score' => 88,
            'status' => 'passed',
        ]);
        $paymentId = DB::table('payments')->insertGetId([
            'reference' => 'PAY-FLOW-SECOND',
            'user_id' => $student->id,
            'payable_type' => Enrolment::class,
            'payable_id' => $secondEnrolment->id,
            'amount' => 2400,
            'currency' => 'GHS',
            'gateway' => 'manual',
            'status' => 'paid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($student)->get(route('student.attendance.index', ['enrolment' => $secondEnrolment->id]))
            ->assertOk()->assertSee($secondCourse->name)->assertSee('1/1');
        $this->actingAs($student)->get(route('student.assessments.index', ['enrolment' => $secondEnrolment->id]))
            ->assertOk()->assertSee('Advanced fillers practical')->assertSee('88.00/100.00');
        $this->actingAs($student)->get(route('student.payments.index', ['enrolment' => $secondEnrolment->id]))
            ->assertOk()->assertSee('PAY-FLOW-SECOND')->assertSee($secondCourse->name);
        $this->actingAs($student)->get(route('student.payments.receipt', $paymentId))->assertOk();
        $this->actingAs($student)->get(route('student.attendance.index', ['enrolment' => $otherEnrolment->id]))->assertForbidden();
        $this->actingAs($student)->get(route('student.assessments.index', ['enrolment' => $otherEnrolment->id]))->assertForbidden();
        $this->actingAs($student)->get(route('student.payments.index', ['enrolment' => $otherEnrolment->id]))->assertForbidden();

        $this->actingAs($student)->post(route('student.support.store'), [
            'enrolment_id' => $secondEnrolment->id,
            'category' => 'course_question',
            'subject' => 'Second course question',
            'message' => 'Please confirm the next practical session.',
        ])->assertRedirect();
        $this->assertDatabaseHas('student_support_requests', [
            'student_profile_id' => $profile->id,
            'enrolment_id' => $secondEnrolment->id,
            'subject' => 'Second course question',
        ]);

        $this->actingAs($student)->post(route('student.support.store'), [
            'enrolment_id' => $otherEnrolment->id,
            'category' => 'course_question',
            'subject' => 'Forbidden course question',
            'message' => 'This should not be accepted.',
        ])->assertSessionHasErrors('enrolment_id');

        $this->assertNotSame($firstAssignment->id, $secondAssignment->id);
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

    private function material(Course $course, Enrolment $enrolment, string $title, string $path): CourseMaterial
    {
        return CourseMaterial::query()->create([
            'course_id' => $course->id,
            'enrolment_id' => $enrolment->id,
            'title' => $title,
            'type' => 'document',
            'file_path' => $path,
            'is_published' => true,
        ]);
    }
}
