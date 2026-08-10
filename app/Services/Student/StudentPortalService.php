<?php

namespace App\Services\Student;

use App\Enums\EnrolmentStatus;
use App\Models\AssessmentResult;
use App\Models\Assignment;
use App\Models\AttendanceRecord;
use App\Models\Certificate;
use App\Models\CourseMaterial;
use App\Models\CourseSession;
use App\Models\Enrolment;
use App\Models\InstalmentPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentPortalService
{
    /**
     * Every enrolment owned by the student that is allowed into the portal.
     *
     * @return Collection<int, Enrolment>
     */
    public function portalEnrolments(User $user): Collection
    {
        $profileId = $user->studentProfile?->id;
        if (! $profileId) {
            return collect();
        }

        return Enrolment::query()
            ->with(['course.category', 'course.trainer.user', 'studentProfile'])
            ->where('student_profile_id', $profileId)
            ->whereIn('status', EnrolmentStatus::portalAccessStatuses())
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'partially_paid' THEN 1 ELSE 2 END")
            ->orderByDesc('activated_at')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Owned enrolments that may access learning resources.
     *
     * @return Collection<int, Enrolment>
     */
    public function learningEnrolments(User $user): Collection
    {
        return $this->portalEnrolments($user)
            ->filter(fn (Enrolment $enrolment) => $this->hasLearningModuleAccess($enrolment))
            ->values();
    }

    public function primaryEnrolment(User $user): ?Enrolment
    {
        return $this->portalEnrolments($user)->first();
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardMetrics(User $user): array
    {
        $enrolment = $this->primaryEnrolment($user);
        $profileId = $user->studentProfile?->id;

        if (! $enrolment || ! $profileId) {
            return [
                'enrolment' => null,
                'active_enrolments' => 0,
                'attendance_percentage' => null,
                'outstanding_balance' => 0,
                'upcoming_session' => null,
                'pending_assignments' => 0,
                'certificates' => 0,
                'unread_notifications' => 0,
            ];
        }

        $attendance = $this->attendanceSummary($enrolment);
        $nextSession = $this->nextSession($enrolment);

        return [
            'enrolment' => $enrolment,
            'active_enrolments' => Enrolment::query()
                ->where('student_profile_id', $profileId)
                ->whereIn('status', [EnrolmentStatus::Active->value, EnrolmentStatus::PartiallyPaid->value])
                ->count(),
            'attendance_percentage' => $attendance['percentage'],
            'outstanding_balance' => (float) $enrolment->outstanding_balance,
            'upcoming_session' => $nextSession,
            'pending_assignments' => Assignment::query()
                ->where('course_id', $enrolment->course_id)
                ->where(fn ($query) => $query->whereNull('enrolment_id')->orWhere('enrolment_id', $enrolment->id))
                ->whereDoesntHave('submissions', fn ($q) => $q->where('enrolment_id', $enrolment->id))
                ->count(),
            'certificates' => Certificate::query()
                ->where('student_profile_id', $profileId)
                ->where('status', 'issued')
                ->count(),
            'unread_notifications' => Schema::hasTable('notifications')
                ? $user->unreadNotifications()->count()
                : 0,
        ];
    }

    /**
     * @return array{total: int, present: int, percentage: ?float, required: ?float}
     */
    public function attendanceSummary(Enrolment $enrolment): array
    {
        $rows = AttendanceRecord::query()->where('enrolment_id', $enrolment->id)->get();
        $total = $rows->count();
        $present = $rows->whereIn('status', ['present', 'late', 'excused'])->count();
        $required = data_get($enrolment->course?->attendance_rules, 'minimum_percent');

        return [
            'total' => $total,
            'present' => $present,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : null,
            'required' => is_numeric($required) ? (float) $required : null,
        ];
    }

    public function nextSession(Enrolment $enrolment): ?CourseSession
    {
        return CourseSession::query()
            ->where('course_schedule_id', $enrolment->course_schedule_id)
            ->whereDate('session_date', '>=', now()->toDateString())
            ->orderBy('session_date')
            ->orderBy('starts_at')
            ->first();
    }

    /**
     * @return Collection<int, CourseMaterial>
     */
    public function publishedMaterials(Enrolment $enrolment): Collection
    {
        if (! in_array($enrolment->status, EnrolmentStatus::fullMaterialAccessStatuses(), true)) {
            return collect();
        }

        return CourseMaterial::query()
            ->where('course_id', $enrolment->course_id)
            ->where(fn ($query) => $query->whereNull('enrolment_id')->orWhere('enrolment_id', $enrolment->id))
            ->where('is_published', true)
            ->orderBy('id')
            ->get();
    }

    /**
     * Return every published material available to any learning enrolment owned
     * by the student. Course-wide resources and enrolment-specific resources
     * are both included, without exposing another student's private files.
     *
     * @return Collection<int, CourseMaterial>
     */
    public function publishedMaterialsForUser(User $user): Collection
    {
        $enrolments = $this->learningEnrolments($user);
        if ($enrolments->isEmpty()) {
            return collect();
        }

        $query = CourseMaterial::query()
            ->with(['course', 'enrolment'])
            ->where('is_published', true);

        $this->scopeResourcesToOwnedEnrolments($query, $enrolments);

        return $query
            ->orderBy('course_id')
            ->orderBy('id')
            ->get();
    }

    /**
     * Return every assignment available to the student's learning enrolments.
     *
     * @return Collection<int, Assignment>
     */
    public function assignmentsForUser(User $user): Collection
    {
        $enrolments = $this->learningEnrolments($user);
        if ($enrolments->isEmpty()) {
            return collect();
        }

        $query = Assignment::query()->with(['course', 'enrolment']);
        $this->scopeResourcesToOwnedEnrolments($query, $enrolments);

        return $query
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->orderBy('id')
            ->get();
    }

    /**
     * Resolve the exact owned enrolment that grants access to a course resource.
     * A targeted resource must match that enrolment; a course-wide resource may
     * use any full-access enrolment owned by the student for the same course.
     */
    public function learningEnrolmentForResource(
        User $user,
        int $courseId,
        ?int $resourceEnrolmentId = null,
    ): ?Enrolment {
        $profileId = $user->studentProfile?->id;
        if (! $profileId) {
            return null;
        }

        return Enrolment::query()
            ->with(['course.category', 'course.trainer.user', 'studentProfile'])
            ->where('student_profile_id', $profileId)
            ->where('course_id', $courseId)
            ->whereIn('status', EnrolmentStatus::fullMaterialAccessStatuses())
            ->when($resourceEnrolmentId !== null, fn (Builder $query) => $query->whereKey($resourceEnrolmentId))
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'partially_paid' THEN 1 ELSE 2 END")
            ->orderByDesc('activated_at')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * All sessions for every portal enrolment owned by the student.
     *
     * @return Collection<int, CourseSession>
     */
    public function calendarSessions(User $user): Collection
    {
        $scheduleIds = $this->portalEnrolments($user)
            ->pluck('course_schedule_id')
            ->filter()
            ->unique()
            ->values();

        if ($scheduleIds->isEmpty()) {
            return collect();
        }

        return CourseSession::query()
            ->with('schedule.course')
            ->whereIn('course_schedule_id', $scheduleIds)
            ->orderBy('session_date')
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, InstalmentPlan>
     */
    public function instalmentPlans(Enrolment $enrolment): Collection
    {
        return InstalmentPlan::query()
            ->where('enrolment_id', $enrolment->id)
            ->orderBy('sequence')
            ->get();
    }

    /**
     * @return Collection<int, AssessmentResult>
     */
    public function assessmentResults(Enrolment $enrolment): Collection
    {
        return AssessmentResult::query()
            ->with('assessment')
            ->where('enrolment_id', $enrolment->id)
            ->latest('updated_at')
            ->get();
    }

    public function paymentsForEnrolment(Enrolment $enrolment): Collection
    {
        return DB::table('payments')
            ->where('payable_type', Enrolment::class)
            ->where('payable_id', $enrolment->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function viewOrNoEnrolment(User $user, string $view, array $extra = []): View
    {
        $enrolment = $this->primaryEnrolment($user);

        if (! $enrolment) {
            return view('student.shared.no-enrolment-page', [
                'title' => $extra['title'] ?? 'Student portal',
                'heading' => $extra['heading'] ?? 'Student portal',
            ]);
        }

        return view($view, array_merge(['enrolment' => $enrolment], $extra));
    }

    public function hasLearningModuleAccess(?Enrolment $enrolment): bool
    {
        return $enrolment !== null
            && in_array($enrolment->status, EnrolmentStatus::fullMaterialAccessStatuses(), true);
    }

    /**
     * @param  callable(Collection<int, Enrolment>): array<string, mixed>  $extraForEnrolments
     */
    public function viewOrLearningModule(User $user, string $view, string $title, callable $extraForEnrolments): View
    {
        $enrolments = $this->portalEnrolments($user);
        $enrolment = $enrolments->first();

        if (! $enrolment) {
            return view('student.shared.no-enrolment-page', [
                'title' => $title,
                'heading' => $title,
            ]);
        }

        $learningEnrolments = $enrolments
            ->filter(fn (Enrolment $record) => $this->hasLearningModuleAccess($record))
            ->values();

        if ($learningEnrolments->isEmpty()) {
            return view('student.shared.section-restricted', [
                'title' => $title,
                'heading' => $title,
                'enrolment' => $enrolment,
            ]);
        }

        return view($view, array_merge(
            [
                'enrolment' => $enrolment,
                'enrolments' => $enrolments,
                'learningEnrolments' => $learningEnrolments,
            ],
            $extraForEnrolments($learningEnrolments),
        ));
    }

    /**
     * Constrain a course resource query to course/enrolment pairs actually
     * owned by the current student.
     *
     * @param  Collection<int, Enrolment>  $enrolments
     */
    private function scopeResourcesToOwnedEnrolments(Builder $query, Collection $enrolments): void
    {
        $enrolmentsByCourse = $enrolments->groupBy(fn (Enrolment $enrolment) => (int) $enrolment->course_id);

        $query->where(function (Builder $owned) use ($enrolmentsByCourse): void {
            foreach ($enrolmentsByCourse as $courseId => $courseEnrolments) {
                $enrolmentIds = $courseEnrolments->pluck('id')->map(fn ($id) => (int) $id)->all();

                $owned->orWhere(function (Builder $resource) use ($courseId, $enrolmentIds): void {
                    $resource
                        ->where('course_id', (int) $courseId)
                        ->where(function (Builder $audience) use ($enrolmentIds): void {
                            $audience->whereNull('enrolment_id')->orWhereIn('enrolment_id', $enrolmentIds);
                        });
                });
            }
        });
    }
}
