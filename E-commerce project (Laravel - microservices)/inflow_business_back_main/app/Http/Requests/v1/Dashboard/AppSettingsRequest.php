<?php

namespace App\Http\Requests\v1\Dashboard;

use App\Enums\AuthTypes;
use App\Enums\LoyaltyTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'auth_type' => [
                'sometimes',
                'required',
                Rule::enum(AuthTypes::class),
            ],
            'two_factor' => 'sometimes|required|boolean',


            'loyalty_accrual_delay' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_program_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_bonuses_per_unit' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_program_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_yearly_bonuses' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_program_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_registration_bonuses' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_program_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_birth_date_bonuses' => [
                'sometimes',
                'integer',
                Rule::requiredIf(fn () => $this->get('loyalty_program_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_bonuses_or_money_when_paid' => [
                'sometimes',
                'boolean',
                Rule::requiredIf(fn () => $this->get('loyalty_program_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_accrue_only_on_paid_money' => [
                'sometimes',
                'boolean',
                Rule::requiredIf(fn () => $this->get('loyalty_program_type') !== LoyaltyTypes::NONE),
            ],
            'loyalty_bonus_levels' => [
                'sometimes',
                'array',
                Rule::requiredIf(fn () => $this->get('loyalty_program_type') !== LoyaltyTypes::NONE),
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
            'loyalty_bonus_levels.*.bonus_active_days' => [
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                'integer',
                'gt:0',
            ],
            'loyalty_bonus_levels.*.percent' => [
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                'numeric',
                'gte:0',
            ],
            'loyalty_bonus_levels.*.bonus_pay_percent' => [
                Rule::requiredIf(fn () => is_array($this->get('loyalty_bonus_levels')) && count($this->get('loyalty_bonus_levels')) > 0),
                'numeric',
                'gte:0',
            ],
        ];
    }
}
