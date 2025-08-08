<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeRegistrationPhoneRequest extends FormRequest
{
    use PhoneTrait;

    public function rules(): array
    {
        return [
            'old_phone' => [
                'required',
                'digits_between:10,11',
                Rule::exists('users', 'phone')->whereNull('phone_verified_at')
            ],
            'phone' => [
                'required',
                'digits_between:10,11',
                'unique:users,phone',
            ]
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->old_phone) {
            $this->merge(['old_phone' => $this->formatPhone($this->old_phone)]);
        }
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }
}
