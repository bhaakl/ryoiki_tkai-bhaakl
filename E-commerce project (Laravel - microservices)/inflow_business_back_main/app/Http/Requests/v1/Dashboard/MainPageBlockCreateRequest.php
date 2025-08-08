<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\MainPageTemplates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MainPageBlockCreateRequest extends FormRequest
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
        $templates = MainPageTemplates::cases();
        $templates = array_filter($templates, fn($template) => $template->addable());

        $rules = [
            'template' => [
                'required',
                Rule::in($templates),
            ],
            'title' => 'required',
            'is_active' => 'required|boolean',
        ];

        return $rules;
    }
}
