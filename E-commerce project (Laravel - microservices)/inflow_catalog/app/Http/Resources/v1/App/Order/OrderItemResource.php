<?php

namespace App\Http\Resources\v1\App\Order;

use App\Http\Resources\v1\App\Product\ProductResource;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var OrderItem $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'product_id' => $model->product_id,
            'quantity' => $model->quantity,
            'price' => $model->price,
            'source' => new OrderItemSourceResource((new Product())->fill((array)($model->source)))
        ];
    }
}
