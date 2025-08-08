<?php

namespace App\Http\Resources\v1\Dashboard\Delivery;

use App\Models\DeliveryPriceCondition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryPriceConditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var DeliveryPriceCondition $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'min_amount' => $model->min_amount,
            'max_amount' => $model->max_amount,
            'price' => $model->price
        ];
    }
}
