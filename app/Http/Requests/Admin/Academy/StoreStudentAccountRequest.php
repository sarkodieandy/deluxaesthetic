<?php

namespace App\Http\Requests\Admin\Academy;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('students.create') || $this->user()?->can('students.manage'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'student_number' => ['nullable', 'string', 'max:50', 'unique:student_profiles,student_number'],
            'education_level' => ['nullable', 'string', 'max:120'],
        ];
    }
}
