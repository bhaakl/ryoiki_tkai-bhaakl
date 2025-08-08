<?php

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class IosSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'user_agreement_url' => ['required'],
            'support_url' => ['required'],
            'description' => ['required'],
            'key_words' => ['required'],
        ];
    }
}
