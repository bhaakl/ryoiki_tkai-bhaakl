<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AlphaName implements Rule
{
    /**
     * Проверка на одну или несколько групп из одной или более букв или цифр (или
     * маркеров компонентов букв), каждая из которых разделена одним разделителем (пробел
     * эквивалентен в любом скрипте, апостроф, подчеркивание или дефис).
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        return preg_match('/^(?:[\pL\pN\pM]+[\pZ\'_-])*[\pL\pN\pM]+$/u', $value) > 0;
    }

    /**
     * Получение сообщения об ошибке валидации.
     */
    public function message(): string
    {
        return trans('validation.alpha_name');
    }
}
