<?php

namespace App\Http\Resources\v1\Dashboard\Product;

use App\Http\Resources\v1\ComponentResource;
use App\Http\Resources\v1\Dashboard\Category\CategoryDropoutResource;
use App\Http\Resources\v1\MediaResource;
use App\Http\Resources\v1\TagResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ProductDetailResource extends JsonResource
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
            'article' => $model->article,
            'title' => $model->title,
            'description' => $model->description,
            'categories' => CategoryDropoutResource::collection($model->categories()->orderBy('name')->get()),
            'price' => $model->price,
            'promo_price' => $model->promo_price,
            'discount' => $model->discount,
            'bonus' => $model->bonus,
            'active' => $model->active,
            'new' => $model->new,
            'preorderable' => $model->preorderable,
            'popular' => $model->popular,
            'special' => $model->special,
            'extra' => $model->extra,
            'bonus_multiplier' => $model->bonus_multiplier,
            'by_order' => $model->by_order,
            'max_amount' => $this->when($model->parent_id, 999),
            'main_image' => new MediaResource($model->getFirstMedia('main')),
            'video' => new MediaResource($model->getFirstMedia('video')),
            'extra_images' => MediaResource::collection($model->getMedia('extra')),
            'properties' => $model->getPropertiesForDashboard(),
            'tags' => $this->when(!$model->parent_id, TagResource::collection($model->tags)),
            'components' => ComponentResource::collection($model->components),
            'offers' => OfferResource::collection($model->offers),
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
            'created_at_timestamp' => Carbon::parse($model->created_at)->timestamp,
            'updated_at_timestamp' => Carbon::parse($model->updated_at)->timestamp,
        ];
    }
}
