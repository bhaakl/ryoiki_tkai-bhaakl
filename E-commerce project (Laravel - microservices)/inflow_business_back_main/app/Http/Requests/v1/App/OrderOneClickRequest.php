<?php

namespace App\Http\Requests\v1\App;

use App\Enums\AuthTypes;
use App\Models\AppSetting;
use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;

class OrderOneClickRequest extends FormRequest
{
    use PhoneTrait;

    protected $additional_rules = [];

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
            'product' => [
                'required',
                'integer'
            ],
            'user_name' => 'required',
            'user_phone' => [
                'required',
                'digits_between:10,11'
            ]
        ];

        $rules = array_merge($rules, $this->additional_rules);

        return $rules;
    }

    protected function prepareForValidation()
    {
        /** @var AppSetting $setting */
        $setting = AppSetting::first();
        $this->merge(['registration_type' => $setting->auth_type->value]);

        if (in_array($setting->auth_type->value, [AuthTypes::Email->value, AuthTypes::EmailAndPhone->value])) {
            $this->additional_rules = array_merge($this->additional_rules, ['user_email' => [
                'required',
                'email'
            ]]);
        }

        if ($this->user_phone) {
            $this->merge(['user_phone' => $this->formatPhone($this->user_phone)]);
        }
    }
}
