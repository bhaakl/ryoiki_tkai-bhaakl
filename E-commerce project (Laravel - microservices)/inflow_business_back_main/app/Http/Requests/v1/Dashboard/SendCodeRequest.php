<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendCodeRequest extends FormRequest
{
    use PhoneTrait;

    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'digits_between:10,11',
                Rule::exists('users', 'phone')->whereNull('phone_verified_at')
            ],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }
}
