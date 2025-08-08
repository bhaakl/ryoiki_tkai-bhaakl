<?php

namespace App\Http\Requests\v1;

use App\Enums\DeliveryIcons;
use App\Enums\DeliveryTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryUpdateRequest extends FormRequest
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
            'type' => [
                'sometimes',
                Rule::in(DeliveryTypes::cases())
            ],
            'icon' => [
                'sometimes',
                Rule::in(DeliveryIcons::cases())
            ],
            'name' => [
                'sometimes',
                Rule::unique('tenant.deliveries', 'name')->ignore($this->delivery)
            ],
            'description' => 'sometimes',
            'base_cost' => 'sometimes|gte:0',
            'active' => 'sometimes|boolean',
            'has_intervals' => 'sometimes|boolean',
            'mkad_min' => 'sometimes|integer|gte:0',
            'mkad_max' => 'sometimes|integer|gte:mkad_min',
        ];
    }
}
