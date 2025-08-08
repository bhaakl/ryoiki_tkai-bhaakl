<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;

class CompleteRequest extends FormRequest
{
    use PhoneTrait;

    public function rules(): array
    {
        return [
            'phone' => 'required|digits_between:10,11',
            'code' => 'required|string|max:5',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }
}
