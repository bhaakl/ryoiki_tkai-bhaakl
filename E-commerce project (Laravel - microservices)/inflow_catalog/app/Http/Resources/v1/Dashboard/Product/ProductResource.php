<?php

namespace App\Http\Resources\v1\Dashboard\Product;

use App\Http\Resources\v1\App\Image\ImageResource;
use App\Http\Resources\v1\MediaResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
            'article' => $model->article,
            'title' => $model->title,
            'description' => Str::words($model->description, 30),
            'price' => $model->offers()->min('price'),
            'active' => $model->active,
            'by_order' => $model->by_order,
            'offers_count' => $model->offers()->count(),
            'max_amount' => $this->when($model->parent_id, 999),
            'updated_at' => $model->updated_at,
            'updated_at_timestamp' => Carbon::parse($model->updated_at)->timestamp,
            'main_image' => new MediaResource($model->getFirstMedia('main')),
        ];
    }
}
