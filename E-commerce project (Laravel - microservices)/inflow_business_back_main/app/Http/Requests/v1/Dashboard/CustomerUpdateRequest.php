<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
{
    use PhoneTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes',
            'email' => [
                'sometimes',
                'email',
                'unique:tenant.customers,email,' . $this->id,
            ],
            'phone' => [
                'sometimes',
                'digits_between:10,11',
                'unique:tenant.customers,phone,' . $this->id
            ],
            'birthday' => 'sometimes',
            'push_notifications' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }
}
