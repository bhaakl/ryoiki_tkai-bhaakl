<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
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
            'name' => 'sometimes|required',
            'address' => 'sometimes|required',
            'phone' => 'sometimes|nullable',
            'subway' => 'sometimes|nullable',
            'lon' => 'sometimes|required|decimal:0,6',
            'lat' => 'sometimes|required|decimal:0,6',
            'open' => 'sometimes|nullable|string',
            'active' => 'sometimes|required|boolean',
            'pickup' => 'sometimes|required|boolean',
            'shop' => 'sometimes|required|boolean',
        ];
    }
}
