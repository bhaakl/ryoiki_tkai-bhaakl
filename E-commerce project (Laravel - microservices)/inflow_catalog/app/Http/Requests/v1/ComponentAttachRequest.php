<?php

namespace App\Http\Requests\v1;

use App\Enums\MeasurementUnits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComponentAttachRequest extends FormRequest
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
            'component_id' => [
                'required',
                Rule::exists('tenant.components', 'id')
            ],
            'quantity' => 'required|numeric|gte:0',
            'unit' => [
                'required',
                Rule::in(MeasurementUnits::cases())
            ]
        ];
    }
}
