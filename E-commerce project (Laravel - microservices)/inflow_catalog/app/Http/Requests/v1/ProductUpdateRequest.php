<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'sort' => 'sometimes|numeric',
            'active' => 'sometimes|boolean',
            'new' => 'sometimes|boolean',
            'preorderable' => 'sometimes|boolean',
            'popular' => 'sometimes|boolean',
            'special' => 'sometimes|boolean',
            'extra' => 'sometimes|boolean',
            'bonus_multiplier' => 'sometimes|boolean',
            'by_order' => 'sometimes|boolean',
            'categories' => 'required|array',
            'categories.*' => 'integer|exists:tenant.categories,id',
        ];
    }
}
