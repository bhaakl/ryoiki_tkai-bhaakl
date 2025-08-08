<?php

namespace App\Http\Requests\v1\App;

use App\Enums\AuthTypes;
use App\Models\AppSetting;
use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhoneLoginRequest extends FormRequest
{
    use PhoneTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var AppSetting $setting */
        $setting = AppSetting::first();

        return in_array($setting->auth_type, [AuthTypes::Phone]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'digits_between:10,11'
            ],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }
}
