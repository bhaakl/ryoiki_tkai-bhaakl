<?php

namespace App\Http\Requests\v1;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryRestrictionUpdateRequest extends FormRequest
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
        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($this->delivery);

        $rules = [
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date',
        ];

        if ($delivery->has_intervals) {
            $rules = array_merge($rules, [
                'intervals' => 'sometimes|array',
                'intervals.*' => [
                    Rule::exists('tenant.delivery_intervals', 'id')->where('delivery_id', $delivery->id)
                ]
            ]);
        }

        return $rules;
    }
}
