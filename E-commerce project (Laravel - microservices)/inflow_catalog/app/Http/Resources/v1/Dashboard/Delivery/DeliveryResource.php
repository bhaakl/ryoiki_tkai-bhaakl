<?php

namespace App\Http\Resources\v1\Dashboard\Delivery;

use App\Enums\DeliveryTypes;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Delivery $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'name' => $model->name,
            'type' => $model->type,
            'type_name' => DeliveryTypes::from($model->type)->name(),
            'cost' => $model->priceConditions()->count() > 0 ? $model->priceConditions()->min('price') . '-' . $model->priceConditions()->max('price') : $model->base_cost,
            'has_conditions' => $model->priceConditions()->count() > 0,
            'active' => $model->active,
            'has_intervals' => $model->has_intervals,
            'intervals' => DeliveryIntervalResource::collection($model->intervals),
        ];
    }
}
