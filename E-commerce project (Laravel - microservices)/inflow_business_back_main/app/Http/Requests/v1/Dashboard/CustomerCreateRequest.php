<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\AuthTypes;
use App\Models\AppSetting;
use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerCreateRequest extends FormRequest
{
    use PhoneTrait;

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
        /** @var AppSetting $settings */
        $settings = AppSetting::first();

        $rules = [
            'name' => 'required',
            'birthday' => 'nullable|date|before:today',
            'push_notifications' => 'nullable|boolean',
        ];

        switch ($settings->auth_type) {
            case AuthTypes::Email:
                $rules['email'] = [
                    'required',
                    'email',
                    Rule::unique('tenant.customers', 'email')->whereNotNull('password')
                ];
                $rules['password'] = [
                    'required',
                    //Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
                ];
                $rules['phone'] = [
                    'nullable',
                    'digits_between:10,11'
                ];
                break;
            case AuthTypes::Phone:
                $rules['phone'] = [
                    'required',
                    'digits_between:10,11',
                    'unique:tenant.customers,phone'
                ];
                $rules['email'] = [
                    'nullable',
                    'email'
                ];
                break;
            case AuthTypes::EmailAndPhone:
                $rules['email'] = [
                    'required',
                    'email',
                    Rule::unique('tenant.customers', 'email')->whereNotNull('password')
                ];
                $rules['password'] = [
                    'required',
                    //Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
                ];
                $rules['phone'] = [
                    'required',
                    'digits_between:10,11'
                ];
                break;
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }
}
