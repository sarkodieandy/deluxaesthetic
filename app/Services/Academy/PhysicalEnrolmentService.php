<?php

namespace App\Services\Academy;

use App\Enums\EnrolmentStatus;
use App\Events\EnrolmentActivated;
use App\Events\StudentAccountActivated;
use App\Models\AuditLog;
use App\Models\CourseEnquiry;
use App\Models\CourseSchedule;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;

class PhysicalEnrolmentService
{
    public function __construct(
        private readonly StudentInvitationService $invitations,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createStudentAccount(array $data, User $staff): User
    {
        return DB::transaction(function () use ($data, $staff) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make(Str::password(32)),
                'email_verified_at' => now(),
                'locale' => $data['locale'] ?? 'en',
                'is_active' => false,
            ]);

            $user->assignRole(Role::findOrCreate('Student'));

            StudentProfile::create([
                'user_id' => $user->id,
                'student_number' => $data['student_number'] ?? $this->studentNumberForUser($user->id),
                'phone' => $data['phone'] ?? null,
                'education_level' => $data['education_level'] ?? null,
            ]);

            $this->audit($staff, 'students.create', User::class, $user->id, ['email' => $user->email]);

            return $user->fresh(['studentProfile']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPhysicalEnrolment(StudentProfile $student, array $data, User $staff): Enrolment
    {
        return DB::transaction(function () use ($student, $data, $staff) {
            $schedule = CourseSchedule::query()
                ->whereKey($data['course_schedule_id'])
                ->where('course_id', $data['course_id'])
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (! $schedule) {
                throw ValidationException::withMessages([
                    'course_schedule_id' => 'The selected training schedule is no longer available for this course.',
                ]);
            }

            if (Enrolment::query()
                ->where('student_profile_id', $student->id)
                ->where('course_schedule_id', $schedule->id)
                ->exists()) {
                throw ValidationException::withMessages([
                    'student_profile_id' => 'This student is already enrolled on the selected training schedule.',
                ]);
            }

            $status = EnrolmentStatus::ApplicationPending->value;
            $occupiedCount = $this->occupiedEnrolmentCount($schedule->id);
            $schedule->update(['enrolled_count' => $occupiedCount]);

            if ($this->occupiesScheduleCapacity($status) && $occupiedCount >= $schedule->capacity) {
                throw ValidationException::withMessages([
                    'course_schedule_id' => 'The selected training schedule is full. Choose another date or increase its capacity.',
                ]);
            }

            $enquiry = null;
            if (! empty($data['course_enquiry_id'])) {
                $enquiry = CourseEnquiry::query()
                    ->whereKey($data['course_enquiry_id'])
                    ->whereNull('converted_enrolment_id')
                    ->whereIn('status', ['submitted', 'reviewing', 'contacted', 'qualified'])
                    ->lockForUpdate()
                    ->first();

                if (! $enquiry
                    || ($enquiry->course_id && (int) $enquiry->course_id !== (int) $data['course_id'])
                    || ! $this->enquiryMatchesStudent($enquiry, $student)) {
                    throw new InvalidArgumentException('The selected enquiry no longer matches this student and course.');
                }
            }

            $fee = (float) $data['fee'];
            $discount = (float) ($data['discount'] ?? 0);
            $amountPaid = (float) ($data['amount_paid'] ?? 0);
            $netFee = max(0, $fee - $discount);
            $outstanding = max(0, $netFee - $amountPaid);

            $enrolment = Enrolment::create([
                'reference' => $data['reference'] ?? $this->nextEnrolmentReference(),
                'student_profile_id' => $student->id,
                'course_id' => $data['course_id'],
                'course_schedule_id' => $data['course_schedule_id'],
                'branch_id' => $data['branch_id'] ?? null,
                'trainer_profile_id' => $data['trainer_profile_id'] ?? null,
                'status' => $status,
                'fee' => $netFee,
                'discount' => $discount,
                'deposit_required' => $data['deposit_required'] ?? null,
                'amount_paid' => $amountPaid,
                'outstanding_balance' => $outstanding,
                'currency' => $data['currency'] ?? 'GHS',
                'enrolment_date' => $data['enrolment_date'] ?? now()->toDateString(),
                'physical_verification_date' => $data['physical_verification_date'] ?? null,
                'verified_by' => ! empty($data['physical_verification_date']) ? $staff->id : null,
                'policies_accepted' => (bool) ($data['policies_accepted'] ?? false),
                'internal_notes' => $data['internal_notes'] ?? null,
                'documents' => $data['documents'] ?? null,
            ]);

            $schedule->update([
                'enrolled_count' => $this->occupiedEnrolmentCount($schedule->id),
            ]);

            if ($enquiry) {
                $enquiry->update([
                    'converted_student_profile_id' => $student->id,
                    'converted_enrolment_id' => $enrolment->id,
                    'status' => 'converted',
                ]);
            }

            DB::table('enrolment_status_histories')->insert([
                'enrolment_id' => $enrolment->id,
                'from_status' => null,
                'to_status' => $enrolment->status,
                'changed_by' => $staff->id,
                'notes' => 'Physical enrolment recorded',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->audit($staff, 'enrolments.create', Enrolment::class, $enrolment->id, [
                'reference' => $enrolment->reference,
                'student_profile_id' => $student->id,
            ]);

            return $enrolment->fresh(['course', 'studentProfile.user']);
        });
    }

    public function activateEnrolment(Enrolment $enrolment, User $staff, bool $sendInvitation = true): Enrolment
    {
        if (! $staff->can('enrolments.activate') && ! $staff->can('enrolments.manage')) {
            throw new \RuntimeException('Unauthorized to activate enrolments.');
        }

        return DB::transaction(function () use ($enrolment, $staff, $sendInvitation) {
            $lockedEnrolment = Enrolment::query()
                ->whereKey($enrolment->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $from = $lockedEnrolment->status;
            $schedule = CourseSchedule::query()
                ->whereKey($lockedEnrolment->course_schedule_id)
                ->lockForUpdate()
                ->first();

            if (! $schedule || (int) $schedule->course_id !== (int) $lockedEnrolment->course_id) {
                throw ValidationException::withMessages([
                    'course_schedule_id' => 'This enrolment is not linked to a valid schedule for its course.',
                ]);
            }

            $occupiedWithoutThisEnrolment = $this->occupiedEnrolmentCount(
                $schedule->id,
                $lockedEnrolment->id,
            );

            if ($from === EnrolmentStatus::Active->value) {
                $schedule->update([
                    'enrolled_count' => $occupiedWithoutThisEnrolment + 1,
                ]);

                return $lockedEnrolment->fresh();
            }

            if (! in_array($from, EnrolmentStatus::activationSourceStatuses(), true)) {
                throw ValidationException::withMessages([
                    'status' => 'Completed, graduated, or certified enrolments cannot be reactivated.',
                ]);
            }

            if (! $schedule->is_active) {
                throw ValidationException::withMessages([
                    'course_schedule_id' => 'This training schedule is no longer available. Reactivate or replace it before activating the enrolment.',
                ]);
            }

            if ($occupiedWithoutThisEnrolment >= $schedule->capacity) {
                throw ValidationException::withMessages([
                    'status' => 'This training schedule is full. Increase its capacity before activating this enrolment.',
                ]);
            }

            $lockedEnrolment->update([
                'status' => EnrolmentStatus::Active->value,
                'activated_at' => now(),
                'activated_by' => $staff->id,
                'confirmed_at' => $lockedEnrolment->confirmed_at ?? now(),
            ]);

            $schedule->update([
                'enrolled_count' => $this->occupiedEnrolmentCount($schedule->id),
            ]);

            $user = $lockedEnrolment->studentProfile?->user;
            if ($user) {
                $user->update(['is_active' => true]);
                event(new StudentAccountActivated($user, $lockedEnrolment, $staff));
            }

            DB::table('enrolment_status_histories')->insert([
                'enrolment_id' => $lockedEnrolment->id,
                'from_status' => $from,
                'to_status' => EnrolmentStatus::Active->value,
                'changed_by' => $staff->id,
                'notes' => 'Enrolment activated — portal access granted',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            event(new EnrolmentActivated($lockedEnrolment->fresh(), $staff));

            if ($sendInvitation && $user) {
                $this->invitations->sendPortalInvitation($user, $lockedEnrolment, $staff);
            }

            $this->audit($staff, 'enrolments.activate', Enrolment::class, $lockedEnrolment->id, [
                'from' => $from,
                'to' => EnrolmentStatus::Active->value,
            ]);

            return $lockedEnrolment->fresh();
        });
    }

    public function allocateStudentNumber(int $userId): string
    {
        return $this->studentNumberForUser($userId);
    }

    private function enquiryMatchesStudent(CourseEnquiry $enquiry, StudentProfile $student): bool
    {
        $student->loadMissing('user');

        if ($enquiry->converted_student_profile_id) {
            return (int) $enquiry->converted_student_profile_id === (int) $student->id;
        }

        if ($enquiry->user_id) {
            return (int) $enquiry->user_id === (int) $student->user_id;
        }

        return $student->user
            && mb_strtolower(trim((string) $enquiry->email)) === mb_strtolower(trim((string) $student->user->email));
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

    private function studentNumberForUser(int $userId): string
    {
        return sprintf('STU-%s-%06d', now()->format('Y'), $userId);
    }

    private function nextEnrolmentReference(): string
    {
        return sprintf(
            'ENR-%s-%s',
            now()->format('Y'),
            strtoupper(substr((string) Str::ulid(), -10)),
        );
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function audit(User $staff, string $action, ?string $type, ?int $id, array $context = []): void
    {
        AuditLog::create([
            'user_id' => $staff->id,
            'action' => $action,
            'auditable_type' => $type,
            'auditable_id' => $id,
            'new_values' => $context,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
