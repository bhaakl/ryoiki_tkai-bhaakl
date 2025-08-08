<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddPropertyRequest extends FormRequest
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
            'enums' => 'array|present',
            'enums.*' => [
                'nullable',
                Rule::exists('tenant.property_enums', 'id'),
            ],
            'strings' => 'array|present',
            'strings.*' => 'nullable|array',
            'strings.*.value' => 'required|string',
            'strings.*.property_id' => [
                'required',
                Rule::exists('tenant.properties', 'id')->where('type', 'string')
            ],
        ];
    }
}
