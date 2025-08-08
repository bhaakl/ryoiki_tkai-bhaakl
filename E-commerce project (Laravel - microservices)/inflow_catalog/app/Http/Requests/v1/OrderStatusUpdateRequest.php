<?php

namespace App\Http\Requests\v1;

use App\Enums\OrderStatuses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStatusUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ext_id' => 'nullable|integer',
            'code' => [
                'sometimes',
                Rule::in(OrderStatuses::cases())
            ],
            'name' => [
                'sometimes',
                Rule::unique('tenant.order_statuses', 'name')->ignore($this->order_status?->id)
            ],
            'active' => [
                'sometimes',
                'boolean'
            ]
        ];
    }
}
