<?php

namespace App\Http\Resources\v1\Dashboard\Delivery;

use App\Enums\DeliveryIcons;
use App\Enums\DeliveryTypes;
use App\Http\Resources\v1\App\Store\StoreResource;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryDetailResource extends JsonResource
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
            'icon' => $model->getIcon(),
            'icon_image' => DeliveryIcons::tryFrom($model->getIcon())?->icon(),
            'base_cost' => $model->base_cost,
            'active' => $model->active,
            'has_intervals' => $model->has_intervals,
            'intervals' => DeliveryIntervalResource::collection($model->intervals),
            'conditions' => DeliveryPriceConditionResource::collection($model->priceConditions),
            'restrictions' => DeliveryRestrictionResource::collection($model->restrictions),
            'stores' => StoreResource::collection($model->stores),
        ];
    }
}
