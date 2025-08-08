<?php

namespace App\Http\Resources\v1\Dashboard\Product;

use App\Http\Resources\v1\App\Image\ImageResource;
use App\Http\Resources\v1\ComponentResource;
use App\Http\Resources\v1\MediaResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class OfferDetailResource extends JsonResource
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
            'sort' => $model->sort,
            'article' => $model->article,
            'barcode' => $model->barcode,
            'title' => $model->title,
            'description' => Str::words($model->description, 30),
            'price' => $model->price,
            'promo_price' => $model->promo_price,
            'discount' => $model->discount,
            'active' => $model->active,
            'popular' => $model->popular,
            'by_order' => $model->by_order,
            'max_amount' => $this->when($model->parent_id, 999),
            'properties' => $model->getPropertiesForDashboard(),
            'components' => ComponentResource::collection($model->components),
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
            'created_at_timestamp' => Carbon::parse($model->created_at)->timestamp,
            'updated_at_timestamp' => Carbon::parse($model->updated_at)->timestamp,
        ];
    }
}
