<?php

namespace App\Services\Student;

use App\Enums\EnrolmentStatus;
use App\Models\Assignment;
use App\Models\AssessmentResult;
use App\Models\AttendanceRecord;
use App\Models\Certificate;
use App\Models\CourseMaterial;
use App\Models\CourseSession;
use App\Models\Enrolment;
use App\Models\InstalmentPlan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudentPortalService
{
    public function primaryEnrolment(User $user): ?Enrolment
    {
        $profileId = $user->studentProfile?->id;
        if (! $profileId) {
            return null;
        }

        return Enrolment::query()
            ->with(['course.category', 'course.trainer.user', 'studentProfile'])
            ->where('student_profile_id', $profileId)
            ->whereIn('status', EnrolmentStatus::portalAccessStatuses())
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('activated_at')
            ->orderByDesc('id')
            ->first();
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
     * @param  callable(Enrolment): array<string, mixed>  $extraForEnrolment
     */
    public function viewOrLearningModule(User $user, string $view, string $title, callable $extraForEnrolment): View
    {
        $enrolment = $this->primaryEnrolment($user);

        if (! $enrolment) {
            return view('student.shared.no-enrolment-page', [
                'title' => $title,
                'heading' => $title,
            ]);
        }

        if (! $this->hasLearningModuleAccess($enrolment)) {
            return view('student.shared.section-restricted', [
                'title' => $title,
                'heading' => $title,
                'enrolment' => $enrolment,
            ]);
        }

        return view($view, array_merge(
            ['enrolment' => $enrolment],
            $extraForEnrolment($enrolment),
        ));
    }
}
