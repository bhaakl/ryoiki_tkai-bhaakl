<?php

namespace App\Http\Resources\v1\App\Product;

use App\Http\Resources\v1\MediaResource;
use App\Http\Resources\v1\TagResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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

        return [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'parent_name' => $this->when($model->parent_id, $model->parent?->title),
            'article' => $model->article,
            'title' => $model->title,
            'price' => $model->price,
            'sale_price' => $model->promo_price,
            'badges' => $this->when($model->discount, BadgeResource::collection([collect(['text' => $model->discount])])),
            'by_order' => $model->by_order,
            'has_package' => $model->has_package,
            'max_amount' => $this->when($model->parent_id, 999),
            'photos' => MediaResource::collection($model->parent_id ? $model->parent?->getMedia('*') : $model->getMedia('*')),
            'tags' => $this->when($model->parent_id, TagResource::collection($model->parent->tags)),
        ];
    }
}
