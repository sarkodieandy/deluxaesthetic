<?php

namespace App\Http\Requests\Admin\Academy;

use App\Enums\EnrolmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePhysicalEnrolmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('enrolments.create') || $this->user()?->can('enrolments.manage'));
    }

    public function rules(): array
    {
        return [
            'student_profile_id' => ['required', 'integer', 'exists:student_profiles,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'course_schedule_id' => ['required', 'integer', 'exists:course_schedules,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'trainer_profile_id' => ['nullable', 'integer', 'exists:trainer_profiles,id'],
            'course_enquiry_id' => ['nullable', 'integer', 'exists:course_enquiries,id'],
            'fee' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'enrolment_date' => ['required', 'date'],
            'physical_verification_date' => ['nullable', 'date'],
            'policies_accepted' => ['sometimes', 'boolean'],
            'internal_notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(array_column(EnrolmentStatus::cases(), 'value'))],
            'activate_now' => ['sometimes', 'boolean'],
            'send_invitation' => ['sometimes', 'boolean'],
        ];
    }
}
