<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\SocialNetworks;
use App\Models\Tenant;
use App\Traits\PhoneTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * @mixin Tenant
 */
class ContactInfoUpdateRequest extends FormRequest
{
    use PhoneTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'                  => 'required|string|email|max:255|unique:users,email',
            'phone'                  => 'required|digits_between:10,11|unique:users,phone',
            'social_links'           => 'nullable|array',
            'social_links.*.link'    => [
                'required',
                'string',
                'url',
                'max:255',
            ],
            'social_links.*.network' => [
                'required',
                'string',
                Rule::enum(SocialNetworks::class),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        Log::info('Incoming social_links data:', $this->input('social_links'));
        if ($this->phone) {
            $this->merge(['phone' => $this->formatPhone($this->phone)]);
        }
    }

    public function messages(): array
    {
        return [
            'social_links.*.network.required' => 'Социальная сеть должна быть указана',
            'social_links.*.network.enum' => 'Указана недопустимая социальная сеть. Доступные варианты: ' . implode(', ', array_column(SocialNetworks::cases(), 'name')),
            'social_links.*.link.required' => 'Ссылка на социальную сеть обязательна',
            'social_links.*.link.url' => 'Ссылка должна быть действительным URL',
            'social_links.*.link.max' => 'Ссылка не должна превышать 255 символов',
        ];
    }
}
