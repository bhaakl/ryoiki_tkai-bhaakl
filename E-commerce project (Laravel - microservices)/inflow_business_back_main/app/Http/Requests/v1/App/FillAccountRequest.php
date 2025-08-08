<?php

namespace App\Http\Requests\v1\App;

use App\Enums\AuthTypes;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;

class FillAccountRequest extends FormRequest
{
    use PhoneTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();

        return !$customer->email_verified_at || !$customer->phone_verified_at;
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
        if ($settings->auth_type == AuthTypes::Phone) {
            $add_rules = [
                'email' => [
                    'nullable',
                    'email',
                    'unique:tenant.customers,email'
                ]
            ];
        } else if ($settings->auth_type == AuthTypes::Email) {
            $add_rules = [
                'phone' => [
                    'nullable',
                    'digits_between:10,11',
                    'unique:tenant.customers,phone'
                ]
            ];
        } else {
            $add_rules = [
                'phone' => [
                    'required',
                    'digits_between:10,11',
                    'unique:tenant.customers,phone'
                ]
            ];
        }

        $rules = [
            'name' => 'required'
        ];

        return array_merge($rules, $add_rules);
    }

    protected function prepareForValidation()
    {
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }
}
