<?php

namespace App\Http\Requests\Admin\Academy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('certificates.issue');
    }

    public function rules(): array
    {
        return [
            'enrolment_id' => [
                'required',
                'integer',
                Rule::exists('enrolments', 'id')->whereNull('deleted_at'),
            ],
            'completion_date' => ['required', 'date'],
            'signatory' => ['nullable', 'string', 'max:190'],
            'issue_now' => ['sometimes', 'boolean'],
        ];
    }
}
