<?php

namespace App\Http\Requests\v1;

use App\Enums\DeliveryIcons;
use App\Enums\DeliveryTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryCreateRequest extends FormRequest
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
                'required',
                Rule::in(DeliveryTypes::cases())
            ],
            'icon' => [
                'required',
                Rule::in(DeliveryIcons::cases())
            ],
            'name' => [
                'required',
                Rule::unique('tenant.deliveries', 'name')->ignore($this->delivery)
            ],
            'description' => 'required',
            'base_cost' => 'required|gte:0',
            'active' => 'required|boolean',
            'has_intervals' => 'required|boolean',
            'mkad_min' => 'sometimes|integer|gte:0',
            'mkad_max' => 'sometimes|integer|gte:mkad_min',
        ];
    }
}
