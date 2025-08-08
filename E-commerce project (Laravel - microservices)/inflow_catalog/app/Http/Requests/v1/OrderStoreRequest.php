<?php

namespace App\Http\Requests\v1;

use App\Enums\DeliveryTypes;
use App\Models\Delivery;
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
            'payment_system_id' => 'sometimes|integer',
            'user_id' => [
                'required'
            ],
            'bonus' => 'nullable|integer|gte:0|lte:' . request()->max_bonus,
            'products' => [
                'array',
                'required_without:kits'
            ],
            'products.*.id' => [
                'required',
                Rule::exists('tenant.products', 'id')->where('active', true)->whereNotNull('parent_id')
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
                Rule::exists('tenant.kits', 'id')
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
                'required',
                Rule::exists('tenant.products', 'id')->where('active', true)->whereNotNull('parent_id')
            ],

            'delivery' => [
                'required'
            ],
            'delivery.id' => [
                Rule::exists('tenant.deliveries', 'id')->where('active', true)
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
            'delivery.store' => 'sometimes'
        ];

        $rules = array_merge($rules, $this->additional_rules);

        return $rules;
    }

    protected function prepareForValidation()
    {
        if (request()->delivery && !isset(request()->delivery['id'])) {
            $delivery = Delivery::whereType(DeliveryTypes::PICKUP)->first();
            if ($delivery) {
                $request_delivery = request()->delivery;
                $request_delivery['id'] = $delivery->id;
                $this->merge(['delivery' => $request_delivery]);
            }
        }
        if (request()->delivery && isset(request()->delivery['id']) && Delivery::find(request()->delivery['id'])) {
            /** @var Delivery $delivery */
            $delivery = Delivery::find(request()->delivery['id']);
            if ($delivery->has_intervals) {
                $this->additional_rules = array_merge($this->additional_rules, [
                    'delivery.interval' => [
                        'required',
                        Rule::exists('tenant.delivery_intervals', 'id')->where('delivery_id', $delivery->id),
                    ],
                    'delivery.address' => [
                        'required'
                    ]
                ]);
            } elseif ($delivery->type == DeliveryTypes::PICKUP->value) {
                $this->additional_rules = array_merge($this->additional_rules, [
                    'delivery.store' => [
                        'required',
                        Rule::exists('tenant.stores', 'id')->where('pickup', true)
                    ]
                ]);
            }
            $this->merge(['user_id' => $this->header('user')]);
        }
    }
}
