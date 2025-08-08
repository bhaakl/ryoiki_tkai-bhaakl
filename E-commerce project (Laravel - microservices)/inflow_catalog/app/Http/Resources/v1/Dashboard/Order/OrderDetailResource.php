<?php

namespace App\Http\Resources\v1\Dashboard\Order;

use App\Data\DeliveryData;
use App\Http\Resources\v1\App\Order\OrderItemResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class OrderDetailResource extends JsonResource
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
            'user_phone' => $model->user_phone,
            'user_email' => $model->user_email,
            'status' => new ChiefOrderStatusResource($model->status),
            'payment_system' => $model->getPaymentSystem(),
            'paid' => $model->paid,
            'items' => OrderItemResource::collection($model->items),
            'items_cost' => $model->getTotal(),
            'paid_with_bonus' => $model->paid_with_bonus,
            'comment' => $model->comment,
            'delivery_info' => $model->delivery_info,
            'created_at' => Carbon::parse($model->created_at)->format('H:i d.m.Y'),
            'updated_at' => Carbon::parse($model->updated_at)->format('H:i d.m.Y'),
            'created_at_timestamp' => Carbon::parse($model->created_at)->timestamp,
            'updated_at_timestamp' => Carbon::parse($model->updated_at)->timestamp,
        ];
    }
}
