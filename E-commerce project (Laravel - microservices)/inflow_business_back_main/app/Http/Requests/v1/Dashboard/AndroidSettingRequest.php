<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\AppCategories;
use App\Enums\Languages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AndroidSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'max:30',
            ],
            'short_description' => [
                'required',
                'max:80',
            ],
            'description' => [
                'required',
                'max:4000',
            ],
            'user_agreement_url' => [
                'required',
                'url'
            ],
            'user_delete_form_url' => [
                'required',
                'url'
            ],
            'default_language' => [
                'required',
                Rule::in(Languages::cases())
            ],
            'app_category' => [
                'required',
                Rule::in(AppCategories::cases())
            ],
        ];
    }
}
