<?php

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class IosMediaUploadRequest extends FormRequest
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
            'iphone5_5-8' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg',
            'iphone6_5-11' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg',
            'iphone6_7-14' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg',
            'ipad_pro_3' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg',
            'ios_app_icon' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }
}
