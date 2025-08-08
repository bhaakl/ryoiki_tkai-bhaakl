<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CommentsRequest extends FormRequest
{
    /**
     * Проверка прав пользователя на выполнение этого запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Получение правил проверки, применяемых к данному запросу.
     */
    public function rules(): array
    {
        return [
            'content' => 'required'
        ];
    }
}
