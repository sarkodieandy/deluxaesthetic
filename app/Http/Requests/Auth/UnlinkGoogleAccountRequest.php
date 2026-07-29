<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UnlinkGoogleAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'password' => [$this->user()?->password ? 'required' : 'nullable', 'string'],
        ];
    }
}
