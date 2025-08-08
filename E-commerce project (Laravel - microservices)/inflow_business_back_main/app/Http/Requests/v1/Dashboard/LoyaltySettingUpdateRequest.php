<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\LoyaltyTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoyaltySettingUpdateRequest extends FormRequest
{
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
        return [
            'loyalty_type' => [
                'required',
                Rule::enum(LoyaltyTypes::class),
            ],

            'delay' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_type') !== LoyaltyTypes::NONE),
            ],
            'rate' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_type') !== LoyaltyTypes::NONE),
            ],
            'annual_amount' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_type') !== LoyaltyTypes::NONE),
            ],
            'register_amount' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_type') !== LoyaltyTypes::NONE),
            ],
            'birthday_amount' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_type') !== LoyaltyTypes::NONE),
            ],
            'dont_add_when_paid_with_bonus' => [
                'sometimes',
                'boolean',
                Rule::requiredIf(fn () => $this->get('loyalty_type') !== LoyaltyTypes::NONE),
            ],
            'dont_add_for_bonuses' => [
                'sometimes',
                'boolean',
                Rule::requiredIf(fn () => $this->get('loyalty_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_bonus_levels' => [
                'sometimes',
                'array',
                Rule::requiredIf(fn () => $this->get('loyalty_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_bonus_levels.*.id' => [
                'sometimes',
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                Rule::exists('bonus_levels', 'id'),
            ],
            'loyalty_bonus_levels.*.name' => [
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                'string',
                'max:255',
            ],
            'loyalty_bonus_levels.*.threshold' => [
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                'integer',
            ],
            'loyalty_bonus_levels.*.bonus_expiration_days' => [
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                'integer',
                'gt:0',
            ],
            'loyalty_bonus_levels.*.percent' => [
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                'numeric',
                'gte:0',
            ],
            'loyalty_bonus_levels.*.buy_percent' => [
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                'numeric',
                'gte:0',
            ],
        ];
    }
}
