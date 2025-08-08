<?php

namespace App\Http\Resources\v1\Dashboard\Order;

use App\Http\Resources\v1\App\Order\OrderStatusResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Order $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'ext_id' => $model->ext_id,
            'user_id' => $model->user_id,
            'user_name' => $model->user_name,
            'total' => $model->getTotalWithDelivery(),
            'status' => new ChiefOrderStatusResource($model->status),
            'paid' => $model->paid,
            'created_at' => Carbon::parse($model->created_at)->format('H:i d.m.Y'),
            'created_at_timestamp' => Carbon::parse($model->created_at)->timestamp,
        ];
    }
}
