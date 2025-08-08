<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class MainSettingsUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'icon_name' => 'required|string|max:255',
            'splash_screen_text' => 'nullable',
            'jivo' => 'nullable',
            'primary' => [
                'required',
                new HexColor
            ],
            'secondary' => [
                'required',
                new HexColor
            ],
            'background_1' => [
                'required',
                new HexColor
            ],
            'background_2' => [
                'required',
                new HexColor
            ],
            'icon' => [
                'required',
                new HexColor
            ],
            'text' => [
                'required',
                new HexColor
            ],
            'gradient_1' => [
                'required',
                new HexColor
            ],
            'gradient_2' => [
                'required',
                new HexColor
            ],
        ];
    }
}
