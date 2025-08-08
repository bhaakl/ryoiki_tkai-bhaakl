<?php

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|min:8|max:255',
            'repeat_password' => 'required|same:password',
            'token' => 'required|string',
        ];
    }
}
