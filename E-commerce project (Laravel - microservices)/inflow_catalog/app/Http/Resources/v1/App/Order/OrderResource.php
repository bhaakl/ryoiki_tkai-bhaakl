<?php

namespace App\Http\Resources\v1\App\Order;

use App\Data\DeliveryData;
use App\Enums\OrderStatuses;
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
            'user_id' => $model->user_id,
            'user_name' => $model->user_name,
            'user_phone' => $model->user_phone,
            'user_email' => $model->user_email,
            'status' => new OrderStatusResource($model->status),
            'payment_system' => $model->getPaymentSystem(),
            'paid_with_bonus' => $model->paid_with_bonus,
            'paid' => $model->paid,
            'canceled' => $model->status->code == OrderStatuses::CANCELED->value,
            'quick' => $model->quick,
            'initial_total' => $model->getInitialTotal(),
            'total' => $model->getTotal(),
            'discount_percent' => $model->getDiscountPercent(),
            'discount_value' => $model->getInitialTotal() - $model->getTotal(),
            'total_with_delivery' => $model->getTotalWithDelivery(),
            'comment' => $model->comment,
            'delivery_info' => $model->delivery_info,
            'items' => OrderItemResource::collection($model->items),
            'created_at' => $model->created_at,
            'created_at_timestamp' => Carbon::parse($model->created_at)->timestamp,
        ];
    }
}
