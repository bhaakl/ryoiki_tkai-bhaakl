<?php

namespace App\Http\Requests\v1;

use App\Enums\MeasurementUnits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
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
            "article" => 'nullable|string',
            "title" => 'required|string',
            "description" => 'required|string',
            "simple" => 'sometimes|boolean',
            "barcode" => 'sometimes|nullable|string',
            "price" => 'required_if:simple,1|nullable|numeric|gte:0',
            "promo_price" => 'nullable|numeric|gte:0',
            "discount" => 'nullable|numeric|gte:0',
            "bonus" => 'sometimes|nullable|numeric|gte:0',
            "bonus_multiplier" => 'sometimes|boolean',
            "sort" => 'sometimes|numeric',
            "active" => 'sometimes|boolean',
            "new" => 'sometimes|boolean',
            "preorderable" => 'sometimes|boolean',
            "popular" => 'sometimes|boolean',
            "special" => 'sometimes|boolean',
            "extra" => 'sometimes|boolean',
            "by_order" => 'sometimes|boolean',
            "categories" => 'required|array',
            'categories.*' => 'integer|exists:tenant.categories,id',
            'components' => 'nullable|array',
            'components.*.id' => [
                'required',
                Rule::exists('tenant.components', 'id')
            ],
            'components.*.quantity' => 'required|numeric|gte:0',
            'components.*.unit' => [
                'required',
                Rule::in(MeasurementUnits::cases())
            ],
            'enums' => 'array|nullable',
            'enums.*' => [
                'nullable',
                Rule::exists('tenant.property_enums', 'id'),
            ],
            'strings' => 'array|nullable',
            'strings.*' => 'nullable|array',
            'strings.*.value' => 'required|string',
            'strings.*.property_id' => [
                'required',
                Rule::exists('tenant.properties', 'id')->where('type', 'string')
            ],
        ];
    }
}
