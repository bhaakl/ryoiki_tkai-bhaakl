<?php

namespace App\Http\Requests\v1\App;

use App\Enums\AuthTypes;
use App\Models\AppSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class EmailRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $settings = AppSetting::first();

        return in_array($settings->auth_type, [AuthTypes::Email, AuthTypes::EmailAndPhone]);
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('tenant.customers', 'email')->whereNotNull('password')
            ],
            'password' => [
                'required',
                //Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ]
        ];
    }
}
