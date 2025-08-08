<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\MenuKeyIcons;
use App\Enums\MenuKeyTemplates;
use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemUpdateRequest extends FormRequest
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
        $rules = [
            'position' => 'nullable|integer',
            'icon' => [
                'nullable',
                Rule::in(MenuKeyIcons::cases()),
            ]
        ];
        $item = MenuItem::find($this->item);
        if ($item) {
            if ($item->value->isCustom()) {
                $rules = array_merge($rules, [
                    'content' => 'sometimes|nullable|string',
                ]);
            }
            if ($item->value->renameable()) {
                $rules = array_merge($rules, [
                    'title' => 'sometimes|nullable|string',
                ]);
            }
            if (!in_array($item->value, [MenuKeyTemplates::SETTINGS, MenuKeyTemplates::ABOUT_APP])) {
                $rules = array_merge($rules, [
                    'is_active' => 'nullable|boolean',
                ]);
            }
        }

        return $rules;
    }
}
