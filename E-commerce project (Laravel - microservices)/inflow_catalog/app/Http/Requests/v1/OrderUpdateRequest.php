<?php

namespace App\Http\Requests\v1;

use App\Enums\DeliveryTypes;
use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends FormRequest
{
    protected $additional_rules = [];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'bonus' => 'nullable|integer|gte:0|lte:' . request()->max_bonus,
            'comment' => 'nullable',
            'payment_system_id' => 'nullable|integer',
            'delivery' => [
                'sometimes'
            ],
            'delivery.id' => [
                'sometimes',
                Rule::exists('tenant.deliveries', 'id')->where('active', true)
            ],
            'delivery.date' => [
                'sometimes',
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
                'sometimes',
                'required'
            ],
            'delivery.recipient_phone' => [
                'sometimes',
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
            'delivery.cost' => [
                'sometimes',
                'required',
                'integer',
                'gte:0'
            ],
            'delivery.courier_name' => [
                'nullable'
            ],
            'delivery.courier_phone' => [
                'nullable'
            ],
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
                    'delivery.date' => [
                        'required',
                        'date'
                    ],
                    'delivery.address' => [
                        'required'
                    ]
                ]);
            } elseif ($delivery->type == DeliveryTypes::PICKUP) {
                $this->additional_rules = array_merge($this->additional_rules, [
                    'delivery.store' => [
                        'required',
                        Rule::exists('stores')->where('pickup', true)
                    ]
                ]);
            }
            $this->merge(['user_id' => $this->header('user')]);
        }
    }
}
