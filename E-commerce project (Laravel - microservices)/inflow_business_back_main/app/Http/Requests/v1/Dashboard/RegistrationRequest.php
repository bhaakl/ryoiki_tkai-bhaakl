<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    use PhoneTrait;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'repeat_password' => 'required|same:password',
            'phone' => 'required|digits_between:10,11|unique:users,phone',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }
}
