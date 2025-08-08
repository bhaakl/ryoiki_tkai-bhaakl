<?php

namespace App\Http\Resources\v1\App;

use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Promo $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'title' => $model->title,
            'description' => $model->description,
            'category_id' => $model->category_id,
            'main_image' => new MediaResource($model->getFirstMedia('main')),
            'images' => MediaResource::collection($model->getMedia('slides')),
        ];
    }
}
