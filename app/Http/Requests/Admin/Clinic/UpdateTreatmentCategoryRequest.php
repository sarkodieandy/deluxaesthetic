<?php

namespace App\Http\Requests\Admin\Clinic;

class UpdateTreatmentCategoryRequest extends StoreTreatmentCategoryRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('treatments.update');
    }
}
