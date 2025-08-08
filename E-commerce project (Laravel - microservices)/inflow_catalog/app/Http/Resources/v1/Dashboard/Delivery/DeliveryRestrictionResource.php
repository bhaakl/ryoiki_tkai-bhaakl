<?php

namespace App\Http\Resources\v1\Dashboard\Delivery;

use App\Models\DeliveryRestriction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryRestrictionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var DeliveryRestriction $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'date_from' => Carbon::parse($model->date_from)->format('Y-m-d'),
            'date_to' => Carbon::parse($model->date_to)->format('Y-m-d'),
            'intervals' => DeliveryIntervalResource::collection($model->intervals)
        ];
    }
}
