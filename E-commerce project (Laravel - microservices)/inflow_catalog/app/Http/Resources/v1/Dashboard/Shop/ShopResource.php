<?php

namespace App\Http\Resources\v1\Dashboard\Shop;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource;
        return [
            'total_paid_amount' => $data->paidPays,
            'total_orders' => $data->totalOrders,
            'total_items_catalog' => $data->totalItems,
        ];
    }
}
