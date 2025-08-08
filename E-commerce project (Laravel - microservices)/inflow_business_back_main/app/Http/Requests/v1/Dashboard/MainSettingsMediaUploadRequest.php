<?php

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class MainSettingsMediaUploadRequest extends FormRequest
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
            'logo_svg' => 'sometimes|file|image|mimes:svg',
            'logo_pdf' => 'sometimes|file|mimes:pdf',
            'logo_png' => 'sometimes|file|image|mimes:png',
        ];
    }
}
