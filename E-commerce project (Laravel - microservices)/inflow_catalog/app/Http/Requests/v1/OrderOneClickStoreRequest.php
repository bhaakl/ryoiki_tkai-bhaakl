<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderOneClickStoreRequest extends FormRequest
{
    protected $additional_rules = [];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'product' => [
                'required',
                Rule::exists('tenant.products', 'id')
                    ->where('active', true)
                    ->whereNotNull('parent_id')
            ],
            'user_name' => 'required',
            'user_phone' => 'required',
            'registration_type' => [
                'required',
                Rule::in([
                    'email',
                    'phone',
                    'email_phone'
                ])
            ]
        ];

        $rules = array_merge($rules, $this->additional_rules);

        return $rules;
    }

    public function prepareForValidation()
    {
        if ($this->registration_type && in_array($this->registration_type, ['email', 'email_phone'])) {
            $this->additional_rules = array_merge($this->additional_rules, ['user_email' => [
                'required',
                'email'
            ]]);
        }
    }
}
