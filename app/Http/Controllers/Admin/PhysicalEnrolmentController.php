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
use Illuminate\View\View;

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
        return view('admin.physical-enrolment.create', [
            'students' => StudentProfile::query()->with('user')->latest()->limit(100)->get(),
            'courses' => Course::query()->where('is_active', true)->orderBy('name')->get(),
            'schedules' => CourseSchedule::query()->where('is_active', true)->orderBy('starts_on')->get(),
            'branches' => Branch::query()->where('is_active', true)->get(),
            'enquiries' => CourseEnquiry::query()->where('status', 'submitted')->latest()->limit(50)->get(),
            'selectedStudentId' => request()->integer('student'),
            'selectedEnquiryId' => request()->integer('enquiry'),
        ]);
    }

    public function store(StorePhysicalEnrolmentRequest $request): RedirectResponse
    {
        $student = StudentProfile::query()->findOrFail($request->integer('student_profile_id'));
        $enrolment = $this->enrolments->createPhysicalEnrolment($student, $request->validated(), $request->user());

        if ($request->boolean('activate_now')) {
            $this->enrolments->activateEnrolment($enrolment, $request->user(), $request->boolean('send_invitation', true));
        }

        return redirect()
            ->route('admin.enrolments.edit', $enrolment)
            ->with('status', 'Physical enrolment recorded.');
    }

    public function activate(Enrolment $enrolment): RedirectResponse
    {
        $this->enrolments->activateEnrolment($enrolment, request()->user(), true);

        return back()->with('status', 'Enrolment activated and portal invitation queued.');
    }
}
