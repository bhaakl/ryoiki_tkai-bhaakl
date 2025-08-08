<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MediaLibraryRequest extends FormRequest
{
    /**
     * Проверка прав пользователя на выполнение этого запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Получение правил проверки, которые применяются к данному запросу.
     */
    public function rules(): array
    {
        return [
            'image' => 'required|image',
            'name' => 'nullable|string|max:255'
        ];
    }
}
