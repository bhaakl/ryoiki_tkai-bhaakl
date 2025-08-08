<?php

namespace App\Http\Requests\v1\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class IosContactSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'address' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['required'],
            'copyright' => ['required'],
        ];
    }
}
