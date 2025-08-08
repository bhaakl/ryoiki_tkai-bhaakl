<?php

namespace App\Http\Requests;

use App\Rules\AlphaName;
use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
{
    /**
     * Проверить, авторизован ли пользователь для выполнения данного запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Получение правил валидации, применяемых к данному запросу."
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new AlphaName],
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
        ];
    }
}
