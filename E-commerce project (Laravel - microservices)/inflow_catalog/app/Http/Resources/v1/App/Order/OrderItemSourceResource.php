<?php

namespace App\Http\Resources\v1\App\Order;

use App\Http\Resources\v1\App\Product\BadgeResource;
use App\Http\Resources\v1\MediaResource;
use App\Http\Resources\v1\TagResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemSourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Product $model */
        $model = $this->resource;

        $photos = [];
        if ($model != null) {
            $photos = MediaResource::collection($model->parent_id && Product::whereId($model->parent_id)->exists() ? $model->parent?->getMedia('*') : $model->getMedia('*'));
        }

        return [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'parent_name' => $this->when($model->parent_id, $model->parent?->title),
            'article' => $model->article,
            'title' => $model->title,
            'price' => $model->price,
            'sale_price' => $model->promo_price,
            'photos' => $photos,
            //'tags' => $this->when($model->parent_id, TagResource::collection($model->parent->tags)),
            'max_amount' => $this->when($model->parent_id, 999),
            'has_package' => $model->has_package,
            'by_order' => $model->by_order,
            'badges' => $this->when($model->discount, BadgeResource::collection([collect(['text' => $model->discount])])),
        ];
    }
}
