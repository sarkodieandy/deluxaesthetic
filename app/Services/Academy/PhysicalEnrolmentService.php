<?php

namespace App\Services\Academy;

use App\Enums\EnrolmentStatus;
use App\Events\EnrolmentActivated;
use App\Events\StudentAccountActivated;
use App\Models\AuditLog;
use App\Models\CourseEnquiry;
use App\Models\Enrolment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
                'student_number' => $data['student_number'] ?? $this->nextStudentNumber(),
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
                'status' => $data['status'] ?? EnrolmentStatus::ApplicationPending->value,
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

            if (! empty($data['course_enquiry_id'])) {
                CourseEnquiry::query()->whereKey($data['course_enquiry_id'])->update([
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
            $from = $enrolment->status;

            $enrolment->update([
                'status' => EnrolmentStatus::Active->value,
                'activated_at' => now(),
                'activated_by' => $staff->id,
                'confirmed_at' => $enrolment->confirmed_at ?? now(),
            ]);

            $user = $enrolment->studentProfile?->user;
            if ($user) {
                $user->update(['is_active' => true]);
                event(new StudentAccountActivated($user, $enrolment, $staff));
            }

            DB::table('enrolment_status_histories')->insert([
                'enrolment_id' => $enrolment->id,
                'from_status' => $from,
                'to_status' => EnrolmentStatus::Active->value,
                'changed_by' => $staff->id,
                'notes' => 'Enrolment activated — portal access granted',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            event(new EnrolmentActivated($enrolment->fresh(), $staff));

            if ($sendInvitation && $user) {
                $this->invitations->sendPortalInvitation($user, $enrolment, $staff);
            }

            $this->audit($staff, 'enrolments.activate', Enrolment::class, $enrolment->id, [
                'from' => $from,
                'to' => EnrolmentStatus::Active->value,
            ]);

            return $enrolment->fresh();
        });
    }

    public function allocateStudentNumber(): string
    {
        return $this->nextStudentNumber();
    }

    private function nextStudentNumber(): string
    {
        $year = now()->format('Y');
        $latest = StudentProfile::withTrashed()
            ->where('student_number', 'like', 'STU-'.$year.'-%')
            ->orderByDesc('id')
            ->value('student_number');

        $sequence = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest, $m)) {
            $sequence = ((int) $m[1]) + 1;
        }

        return sprintf('STU-%s-%04d', $year, $sequence);
    }

    private function nextEnrolmentReference(): string
    {
        $latest = Enrolment::withTrashed()->orderByDesc('id')->value('reference');
        if ($latest && preg_match('/(\d+)$/', $latest, $m)) {
            return 'ENR-'.str_pad((string) (((int) $m[1]) + 1), 6, '0', STR_PAD_LEFT);
        }

        return 'ENR-000001';
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
