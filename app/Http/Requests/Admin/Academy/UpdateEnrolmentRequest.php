<?php

namespace App\Http\Requests\Admin\Academy;

use App\Enums\EnrolmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnrolmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('enrolments.manage'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_column(EnrolmentStatus::cases(), 'value'))],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'outstanding_balance' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
