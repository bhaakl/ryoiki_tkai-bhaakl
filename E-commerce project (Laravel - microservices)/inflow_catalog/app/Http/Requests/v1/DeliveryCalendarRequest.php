<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryCalendarRequest extends FormRequest
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
            'products' => 'nullable|array',
            'products.*' => 'integer'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->products) {
            $products = str_replace(' ', '', $this->products);
            $products = explode(',', $products);
            $this->merge(['products' => $products]);
        }
    }
}
