<?php

namespace App\Http\Requests;

use App\Rules\CurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class UserPasswordsRequest extends FormRequest
{
    /**
     * Проверить, авторизован ли пользователь для выполнения данного запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Получение правил валидации, применяемых к данному запросу.
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', new CurrentPassword],
            'password' => 'required|confirmed'
        ];
    }
}
