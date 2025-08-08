<?php

namespace App\Http\Resources\v1\App\Delivery;

use App\Enums\DeliveryIcons;
use App\Enums\DeliveryTypes;
use App\Http\Resources\v1\App\Store\StoreResource;
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
            'description' => $model->description,
            'type' => $model->type,
            'type_name' => DeliveryTypes::from($model->type)->name(),
            'icon' => DeliveryIcons::tryFrom($model->getIcon())?->icon(),
            'base_cost' => $model->base_cost,
            'mkad_min' => $model->mkad_min,
            'mkad_max' => $model->mkad_max,
            'has_intervals' => $model->has_intervals,
            'intervals' => $this->when($model->has_intervals, DeliveryIntervalResource::collection($model->intervals)),
            'conditions' => $this->when($model->priceConditions()->count() > 0, DeliveryPriceConditionResource::collection($model->priceConditions)),
            'stores' => $this->when($model->type == DeliveryTypes::PICKUP->value, StoreResource::collection($model->stores)),
        ];
    }
}
