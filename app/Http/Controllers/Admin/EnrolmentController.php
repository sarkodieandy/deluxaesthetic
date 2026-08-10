<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrolmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academy\UpdateEnrolmentRequest;
use App\Models\CourseSchedule;
use App\Models\Enrolment;
use App\Services\Academy\PhysicalEnrolmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EnrolmentController extends Controller
{
    public function __construct(
        private readonly PhysicalEnrolmentService $physicalEnrolments,
    ) {}

    public function index(): View
    {
        $enrolments = DB::table('enrolments')
            ->join('student_profiles', 'student_profiles.id', '=', 'enrolments.student_profile_id')
            ->join('users', 'users.id', '=', 'student_profiles.user_id')
            ->join('courses', 'courses.id', '=', 'enrolments.course_id')
            ->select('enrolments.*', 'users.name as student_name', 'courses.name as course_name')
            ->whereNull('enrolments.deleted_at')
            ->latest('enrolments.created_at')
            ->paginate(20);

        return view('admin.enrolments.index', compact('enrolments'));
    }

    public function edit(Enrolment $enrolment): View
    {
        $enrolment->load(['studentProfile.user', 'course']);

        return view('admin.enrolments.edit', [
            'enrolment' => $enrolment,
            'studentName' => $enrolment->studentProfile?->user?->name,
            'studentEmail' => $enrolment->studentProfile?->user?->email,
            'courseName' => $enrolment->course?->name,
        ]);
    }

    public function update(UpdateEnrolmentRequest $request, Enrolment $enrolment): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $enrolment, $request) {
            $lockedEnrolment = Enrolment::query()
                ->whereKey($enrolment->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $previousStatus = $lockedEnrolment->status;

            if ($data['status'] === EnrolmentStatus::Active->value
                && $previousStatus !== EnrolmentStatus::Active->value) {
                $lockedEnrolment = $this->physicalEnrolments->activateEnrolment(
                    $lockedEnrolment,
                    $request->user(),
                    false,
                );
                $lockedEnrolment->update([
                    'amount_paid' => $data['amount_paid'] ?? $lockedEnrolment->amount_paid,
                    'outstanding_balance' => $data['outstanding_balance'] ?? $lockedEnrolment->outstanding_balance,
                ]);

                return;
            }

            $schedule = CourseSchedule::query()
                ->whereKey($lockedEnrolment->course_schedule_id)
                ->lockForUpdate()
                ->first();
            $previousOccupiesCapacity = $this->occupiesScheduleCapacity($previousStatus);
            $nextOccupiesCapacity = $this->occupiesScheduleCapacity($data['status']);

            $occupiedWithoutThisEnrolment = $schedule
                ? $this->occupiedEnrolmentCount($schedule->id, $lockedEnrolment->id)
                : 0;

            if ($schedule
                && ! $previousOccupiesCapacity
                && $nextOccupiesCapacity
                && (! $schedule->is_active || $occupiedWithoutThisEnrolment >= $schedule->capacity)) {
                throw ValidationException::withMessages([
                    'status' => ! $schedule->is_active
                        ? 'This training schedule is inactive. Reactivate it before restoring this enrolment.'
                        : 'This training schedule is full. Increase its capacity before restoring this enrolment.',
                ]);
            }

            $lockedEnrolment->update([
                'status' => $data['status'],
                'amount_paid' => $data['amount_paid'] ?? $lockedEnrolment->amount_paid,
                'outstanding_balance' => $data['outstanding_balance'] ?? $lockedEnrolment->outstanding_balance,
            ]);

            if ($previousStatus !== $data['status']) {
                if ($schedule) {
                    $schedule->update([
                        'enrolled_count' => $this->occupiedEnrolmentCount($schedule->id),
                    ]);
                }

                DB::table('enrolment_status_histories')->insert([
                    'enrolment_id' => $lockedEnrolment->id,
                    'from_status' => $previousStatus,
                    'to_status' => $data['status'],
                    'changed_by' => $request->user()?->id,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('admin.enrolments.edit', $enrolment)->with('status', 'Enrolment updated successfully.');
    }

    private function occupiesScheduleCapacity(string $status): bool
    {
        return ! in_array($status, [
            EnrolmentStatus::Cancelled->value,
            EnrolmentStatus::Withdrawn->value,
        ], true);
    }

    private function occupiedEnrolmentCount(int $scheduleId, ?int $exceptEnrolmentId = null): int
    {
        return Enrolment::query()
            ->where('course_schedule_id', $scheduleId)
            ->when(
                $exceptEnrolmentId,
                fn ($query) => $query->where('id', '!=', $exceptEnrolmentId),
            )
            ->whereNotIn('status', [
                EnrolmentStatus::Cancelled->value,
                EnrolmentStatus::Withdrawn->value,
            ])
            ->count();
    }
}
