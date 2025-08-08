<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class CanBeAuthor implements Rule
{
    /**
     * Определение прохождения правила проверки.
     */
    public function passes($attribute, $value)
    {
        $author = User::find($value);

        return $author->canBeAuthor();
    }

    /**
     * Получение сообщения об ошибке валидации.
     */
    public function message(): string
    {
        return trans('validation.can_be_author');
    }
}
