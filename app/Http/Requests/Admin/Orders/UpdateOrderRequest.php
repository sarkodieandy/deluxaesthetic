<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('orders.manage'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['awaiting_payment', 'paid', 'processing', 'ready_for_pickup', 'shipped', 'delivered', 'cancelled', 'returned', 'refunded'])],
            'delivery_status' => ['required', Rule::in(['pending', 'shipped', 'delivered'])],
            'tracking_number' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
