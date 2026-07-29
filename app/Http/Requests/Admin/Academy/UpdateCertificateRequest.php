<?php

namespace App\Http\Requests\Admin\Academy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return (bool) ($user && ($user->can('certificates.issue') || $user->can('certificates.revoke')));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['draft', 'issued', 'revoked'])],
            'signatory' => ['nullable', 'string', 'max:190'],
        ];
    }
}
