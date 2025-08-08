<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
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
            'name' => 'required',
            'address' => 'required',
            'phone' => 'nullable',
            'subway' => 'nullable',
            'lon' => 'required|decimal:0,6',
            'lat' => 'required|decimal:0,6',
            'open' => 'nullable|string',
            'active' => 'required|boolean',
            'pickup' => 'required|boolean',
            'shop' => 'required|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['shop' => true]);
    }
}
