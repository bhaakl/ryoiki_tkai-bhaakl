<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\MenuKeyIcons;
use App\Enums\MenuKeyTemplates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemCreateRequest extends FormRequest
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
            'title' => 'required',
            'content' => 'required',
            'icon' => [
                'nullable',
                Rule::in(MenuKeyIcons::cases()),
            ],
            'value' => 'sometimes'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['value' => MenuKeyTemplates::HTML_TEXT->value]);
    }
}
