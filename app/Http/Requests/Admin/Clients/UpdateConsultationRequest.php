<?php

namespace App\Http\Requests\Admin\Clients;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) ($user && ($user->can('consultations.respond') || $user->can('consultations.assign') || $user->can('consultations.view')));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['submitted', 'in_review', 'contacted', 'closed'])],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_date' => ['nullable', 'date'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'client_response' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
