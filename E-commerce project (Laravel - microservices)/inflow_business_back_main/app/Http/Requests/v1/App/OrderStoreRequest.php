<?php

namespace App\Http\Requests\v1\App;

use App\Models\PaymentSystem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
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
            'payment_system_id' => 'nullable|exists:payment_systems,id',
            'products' => [
                'array',
                'required_without:kits'
            ],
            'products.*.id' => [
                'required',
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                'gte:1'
            ],

            'kits' => [
                'array',
                'required_without:products'
            ],
            'kits.*.id' => [
                'required',
            ],
            'kits.*.quantity' => [
                'required',
                'integer',
                'gte:1'
            ],
            'kits.*.products' => [
                'required',
                'array'
            ],
            'kits.*.products.*' => [
                'required'
            ],

            'delivery' => [
                'required'
            ],
            'delivery.date' => [
                'nullable',
                'date',
                'after_or_equal:today'
            ],
            'delivery.apartment' => [
                'nullable'
            ],
            'delivery.entrance' => [
                'nullable'
            ],
            'delivery.intercom' => [
                'nullable'
            ],
            'delivery.recipient_name' => [
                'required'
            ],
            'delivery.recipient_phone' => [
                'required'
            ],
            'delivery.recipient_email' => [
                'nullable'
            ],
             'delivery.recipient_note' => [
                'nullable'
            ],
            'delivery.anonymously' => [
                'nullable',
                'boolean'
            ],

        ];

        $rules = array_merge($rules, $this->additional_rules);

        return $rules;
    }

    protected function prepareForValidation()
    {
        if (!$this->payment_system_id) {
            $this->merge(['payment_system_id' => PaymentSystem::first()?->id]);
        }

        $bonus = auth('customer')->user()->getBonusBalance();
        if ($bonus != null) {
            $this->merge(['bonus' => 'nullable|integer|gte:0|lte:' . $bonus]);
        }
    }
}
