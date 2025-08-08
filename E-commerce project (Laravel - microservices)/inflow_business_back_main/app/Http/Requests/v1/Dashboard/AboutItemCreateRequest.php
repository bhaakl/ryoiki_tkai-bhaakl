<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\AboutTemplates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use function Symfony\Component\String\b;

class AboutItemCreateRequest extends FormRequest
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
            "type" => [
                'required',
                Rule::in(AboutTemplates::cases())
            ],
            'title' => 'required|string',
        ];
    }
}
