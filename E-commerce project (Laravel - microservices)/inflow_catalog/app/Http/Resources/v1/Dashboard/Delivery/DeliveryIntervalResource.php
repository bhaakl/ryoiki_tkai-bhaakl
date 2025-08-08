<?php

namespace App\Http\Resources\v1\Dashboard\Delivery;

use App\Models\DeliveryInterval;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryIntervalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var DeliveryInterval $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'interval' => $model->interval,
            'time_from' => $model->time_from,
            'time_to' => $model->time_to,
        ];
    }
}
