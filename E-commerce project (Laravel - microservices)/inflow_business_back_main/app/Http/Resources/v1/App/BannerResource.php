<?php

namespace App\Http\Resources\v1\App;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Banner $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'title' => $model->title,
            'category_id' => $model->category_id,
            'image' => new MediaResource($model->getFirstMedia())
        ];
    }
}
