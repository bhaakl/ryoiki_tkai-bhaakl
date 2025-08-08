<?php

namespace App\Http\Resources\v1\App\Order;

use App\Enums\OrderStatuses;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var OrderStatus $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'code' => $model->code,
            'name' => $model->name,
            'is_initial' => OrderStatuses::from($model->code)->isInitial(),
            'is_final' => OrderStatuses::from($model->code)->isFinal(),
            'is_success' => OrderStatuses::from($model->code)->isSuccessful(),
            'is_canceled' => OrderStatuses::from($model->code)->isCanceled(),
        ];
    }
}
