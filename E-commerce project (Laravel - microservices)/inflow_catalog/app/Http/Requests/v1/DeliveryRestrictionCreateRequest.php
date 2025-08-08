<?php

namespace App\Http\Requests\v1;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryRestrictionCreateRequest extends FormRequest
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
            'date_from' => 'required|date',
            'date_to' => 'required|date',
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
