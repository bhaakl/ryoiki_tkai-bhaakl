<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'collection' => [
                'required',
                Rule::in(Media::COLLECTIONS),
            ],
        ];
    }
}
