<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academy\StorePhysicalEnrolmentRequest;
use App\Http\Requests\Admin\Academy\StoreStudentAccountRequest;
use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseEnquiry;
use App\Models\CourseSchedule;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\Academy\PhysicalEnrolmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use InvalidArgumentException;

class PhysicalEnrolmentController extends Controller
{
    public function __construct(
        private readonly PhysicalEnrolmentService $enrolments,
    ) {}

    public function createStudent(): View
    {
        return view('admin.physical-enrolment.create-student');
    }

    public function storeStudent(StoreStudentAccountRequest $request): RedirectResponse
    {
        $user = $this->enrolments->createStudentAccount($request->validated(), $request->user());

        return redirect()
            ->route('admin.physical-enrolment.create', ['student' => $user->studentProfile?->id])
            ->with('status', 'Student account created. Assign course and activate when physical registration is complete.');
    }

    public function create(): View
    {
        $selectedEnquiryId = request()->integer('enquiry');
        $selectedEnquiry = $selectedEnquiryId
            ? CourseEnquiry::query()->with('user.studentProfile')->find($selectedEnquiryId)
            : null;
        $selectedStudentId = request()->integer('student')
            ?: $selectedEnquiry?->converted_student_profile_id
            ?: $selectedEnquiry?->user?->studentProfile?->id;
        $selectedCourseId = request()->integer('course') ?: $selectedEnquiry?->course_id;
        $selectedScheduleId = request()->integer('schedule');

        if (! $selectedScheduleId && $selectedCourseId) {
            $selectedScheduleId = CourseSchedule::query()
                ->where('course_id', $selectedCourseId)
                ->where('is_active', true)
                ->orderBy('starts_on')
                ->value('id');
        }

        $students = StudentProfile::query()->with('user')->latest()->limit(100)->get();
        $studentByUser = $students->keyBy('user_id');
        $studentByEmail = $students->filter(fn ($student) => $student->user?->email)
            ->keyBy(fn ($student) => mb_strtolower(trim((string) $student->user->email)));
        $enquiries = CourseEnquiry::query()
            ->with(['user.studentProfile', 'course'])
            ->whereNull('converted_enrolment_id')
            ->whereIn('status', ['submitted', 'reviewing', 'contacted', 'qualified'])
            ->latest()->limit(50)->get();

        $enquiries->each(function (CourseEnquiry $enquiry) use ($studentByUser, $studentByEmail): void {
            $profile = $enquiry->converted_student_profile_id
                ? StudentProfile::query()->find($enquiry->converted_student_profile_id)
                : ($studentByUser->get($enquiry->user_id)
                    ?: $studentByEmail->get(mb_strtolower(trim((string) $enquiry->email))));
            $enquiry->setAttribute('matched_student_profile_id', $profile?->id);
        });

        return view('admin.physical-enrolment.create', [
            'students' => $students,
            'courses' => Course::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'schedules' => CourseSchedule::query()->with('course')->where('is_active', true)->orderBy('starts_on')->get(),
            'branches' => Branch::query()->where('is_active', true)->get(),
            'enquiries' => $enquiries,
            'selectedStudentId' => $selectedStudentId,
            'selectedCourseId' => $selectedCourseId,
            'selectedScheduleId' => $selectedScheduleId,
            'selectedEnquiryId' => $selectedEnquiryId,
            'canActivate' => (bool) (request()->user()?->can('enrolments.activate') || request()->user()?->can('enrolments.manage')),
        ]);
    }

    public function store(StorePhysicalEnrolmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $enrolment = DB::transaction(function () use ($request, $data) {
                $student = StudentProfile::query()->findOrFail((int) $data['student_profile_id']);
                $enrolment = $this->enrolments->createPhysicalEnrolment($student, $data, $request->user());

                if ((bool) ($data['activate_now'] ?? false)) {
                    $enrolment = $this->enrolments->activateEnrolment(
                        $enrolment,
                        $request->user(),
                        (bool) ($data['send_invitation'] ?? false),
                    );
                }

                return $enrolment;
            });
        } catch (InvalidArgumentException $exception) {
            return back()->withInput()->withErrors(['course_enquiry_id' => $exception->getMessage()]);
        }

        $destination = $request->user()?->can('enrolments.manage')
            ? route('admin.enrolments.edit', $enrolment)
            : route('admin.physical-enrolment.create');

        return redirect($destination)->with('status', 'Physical enrolment recorded.');
    }

    public function activate(Enrolment $enrolment): RedirectResponse
    {
        $this->enrolments->activateEnrolment($enrolment, request()->user(), true);

        return back()->with('status', 'Enrolment activated and portal invitation queued.');
    }
}
