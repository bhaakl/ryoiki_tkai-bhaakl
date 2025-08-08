<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\AboutTemplates;
use App\Models\AboutItem;
use Illuminate\Foundation\Http\FormRequest;

class AboutImageUploadRequest extends FormRequest
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

        $rules = [];

        if ($item) {
            switch ($item->type) {
                case AboutTemplates::photo:
                    $rules = [
                        'images' => 'required|array',
                        'images.*' => 'required|file|image|mimes:jpeg,png,jpg|max:3072|dimensions:max_width=1920,max_height=1280',
                    ];
                    break;
                case AboutTemplates::license:
                    $rules = [
                        'name' => 'required|string',
                        'image' => 'required|file|image|mimes:jpeg,png,jpg|max:3072|dimensions:max_width=1920,max_height=1280',
                        'description' => 'required|string',
                    ];
                    break;
            }
        }

        return $rules;
    }
}
