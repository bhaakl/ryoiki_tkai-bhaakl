<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CurrentPassword implements Rule
{
    /**
     * Определение прохождения правила проверки.
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, auth()->user()->password);
    }

    /**
     * Получение сообщения об ошибке валидации.
     */
    public function message(): string
    {
        return trans('validation.current_password');
    }
}
