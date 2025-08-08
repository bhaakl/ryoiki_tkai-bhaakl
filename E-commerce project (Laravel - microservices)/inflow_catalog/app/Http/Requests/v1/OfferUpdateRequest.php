<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class OfferUpdateRequest extends FormRequest
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
            'article' => 'sometimes|nullable|string',
            'barcode' => 'nullable|string',
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|numeric|gte:0',
            'promo_price' => 'sometimes|nullable|numeric|gte:0',
            'discount' => 'sometimes|nullable|numeric|gte:0',
            'bonus' => 'sometimes|nullable|numeric|gte:0',
            'bonus_multiplier' => 'sometimes|boolean',
            'sort' => 'sometimes|numeric',
            'active' => 'sometimes|boolean',
            'popular' => 'sometimes|boolean',
            'by_order' => 'sometimes|boolean',
        ];
    }
}
