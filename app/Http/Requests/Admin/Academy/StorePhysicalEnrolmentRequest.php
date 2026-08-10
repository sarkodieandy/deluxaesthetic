<?php

namespace App\Http\Requests\Admin\Academy;

use App\Models\CourseEnquiry;
use App\Models\StudentProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePhysicalEnrolmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('enrolments.create') || $this->user()?->can('enrolments.manage'));
    }

    public function rules(): array
    {
        $canActivate = (bool) ($this->user()?->can('enrolments.activate') || $this->user()?->can('enrolments.manage'));

        return [
            'student_profile_id' => ['required', 'integer', 'exists:student_profiles,id'],
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')->where(fn ($query) => $query->where('is_active', true)->whereNull('deleted_at'))],
            'course_schedule_id' => ['required', 'integer', Rule::exists('course_schedules', 'id')->where(fn ($query) => $query
                ->where('course_id', $this->input('course_id'))
                ->where('is_active', true))],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'trainer_profile_id' => ['nullable', 'integer', 'exists:trainer_profiles,id'],
            'course_enquiry_id' => ['nullable', 'integer', Rule::exists('course_enquiries', 'id')
                ->whereNull('deleted_at')
                ->whereNull('converted_enrolment_id')
                ->whereIn('status', ['submitted', 'reviewing', 'contacted', 'qualified'])],
            'fee' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['GHS', 'USD'])],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'enrolment_date' => ['required', 'date'],
            'physical_verification_date' => ['nullable', 'date'],
            'policies_accepted' => ['sometimes', 'boolean'],
            'internal_notes' => ['nullable', 'string'],
            'activate_now' => [Rule::prohibitedIf(! $canActivate), 'sometimes', 'boolean'],
            'send_invitation' => [Rule::prohibitedIf(! $canActivate || ! $this->boolean('activate_now')), 'sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $enquiryId = $this->integer('course_enquiry_id');
            $studentId = $this->integer('student_profile_id');
            $courseId = $this->integer('course_id');

            if (! $enquiryId || ! $studentId || ! $courseId || $validator->errors()->isNotEmpty()) {
                return;
            }

            $enquiry = CourseEnquiry::query()->find($enquiryId);
            $student = StudentProfile::query()->with('user')->find($studentId);
            if (! $enquiry || ! $student) {
                return;
            }

            if ($enquiry->course_id && (int) $enquiry->course_id !== $courseId) {
                $validator->errors()->add('course_enquiry_id', 'The selected enquiry belongs to a different course.');
            }

            if (! $this->enquiryMatchesStudent($enquiry, $student)) {
                $validator->errors()->add('course_enquiry_id', 'The selected enquiry belongs to a different student.');
            }
        }];
    }

    private function enquiryMatchesStudent(CourseEnquiry $enquiry, StudentProfile $student): bool
    {
        if ($enquiry->converted_student_profile_id) {
            return (int) $enquiry->converted_student_profile_id === (int) $student->id;
        }

        if ($enquiry->user_id) {
            return (int) $enquiry->user_id === (int) $student->user_id;
        }

        return $student->user
            && mb_strtolower(trim((string) $enquiry->email)) === mb_strtolower(trim((string) $student->user->email));
    }
}
