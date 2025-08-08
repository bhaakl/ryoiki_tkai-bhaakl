<?php

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AndroidMediaUploadRequest extends FormRequest
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
            'market_banner' => 'sometimes|file|image|mimes:jpeg,png,jpg|dimensions:max_width=1024,max_height=500',
            'market_image' => 'sometimes|file|image|mimes:jpeg,png,jpg|dimensions:min_width=320,min_height=320,max_width=3840,max_height=3840,ratio=16/19',
            'android_app_icon' => 'sometimes|file|image|mimes:jpeg,png,jpg',
        ];
    }
}
