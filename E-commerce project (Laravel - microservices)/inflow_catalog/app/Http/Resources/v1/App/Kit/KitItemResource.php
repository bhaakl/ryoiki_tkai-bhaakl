<?php

namespace App\Http\Resources\v1\App\Kit;

use App\Http\Resources\v1\App\Product\ProductResource;
use App\Models\KitItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KitItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var KitItem $model */
        $model = $this->resource;

        $price = $model->product->price;
        if ($model->discount_type == 'percent') {
            $item_price = floor($price - $price / 100 * $model->discount_value);
        } else {
            $item_price = $price - $model->discount_value;
        }

        return [
            'id' => $model->id,
            'product_id' => $model->product_id,
            'discount_type' => $model->discount_type,
            'discount_value' => $model->discount_value,
            'price' => $item_price,
            'alt_category_id' => $model->alt_category_id,
            'product' => new ProductResource($model->product)
        ];
    }
}
