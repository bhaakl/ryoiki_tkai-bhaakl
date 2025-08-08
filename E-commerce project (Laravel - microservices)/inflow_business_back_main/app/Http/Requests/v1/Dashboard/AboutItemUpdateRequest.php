<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\AboutTemplates;
use App\Models\AboutItem;
use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AboutItemUpdateRequest extends FormRequest
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
        /** @var AboutItem $item */
        $item = AboutItem::find($this->route('id'));

        $rules = [
            'title' => 'sometimes|string',
        ];

        if ($item) {
            $rules['text'] = 'sometimes|string';
            if ($item->type == AboutTemplates::license) {
                $rules['uuid'] = [
                    'required',
                    Rule::exists('tenant.media', 'uuid')->where('model_type', AboutItem::class)
                ];
                $rules['name'] = 'sometimes|string';
                $rules['description'] = 'sometimes|string';
            }
        }

        return array_merge($rules, [
            'title' => 'sometimes|string',
        ]);
    }
}
