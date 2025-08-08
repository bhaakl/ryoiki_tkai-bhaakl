<?php

namespace App\Http\Requests\v1\App;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                'exists:tenant.customers,email',
            ]
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'Указанный e-mail не найден среди зарегистрированных пользователей, перепроверьте его или создайте аккаунт'
        ];
    }
}
