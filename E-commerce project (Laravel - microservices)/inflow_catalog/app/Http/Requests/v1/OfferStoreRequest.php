<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class OfferStoreRequest extends FormRequest
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
            'article' => 'nullable|string',
            'barcode' => 'nullable|string',
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'promo_price' => 'nullable|numeric|gte:0',
            'discount' => 'nullable|numeric|gte:0',
            'bonus' => 'sometimes|nullable|numeric|gte:0',
            'bonus_multiplier' => 'sometimes|boolean',
            'sort' => 'sometimes|numeric|gte:0',
            'active' => 'sometimes|boolean',
            'popular' => 'sometimes|boolean',
            'by_order' => 'sometimes|boolean',
        ];
    }
}
