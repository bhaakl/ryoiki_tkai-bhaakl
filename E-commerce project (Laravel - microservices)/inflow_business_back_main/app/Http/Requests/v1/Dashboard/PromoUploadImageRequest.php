<?php

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class PromoUploadImageRequest extends FormRequest
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
            'main_image' => 'sometimes|file|image|mimes:jpeg,png,jpg|max:3072|dimensions:max_width=1920,max_height=1280',
            'slides' => 'sometimes|array',
            'slides.*' => 'required|file|image|mimes:jpeg,png,jpg|max:3072|dimensions:max_width=1920,max_height=1280',
        ];
    }
}
