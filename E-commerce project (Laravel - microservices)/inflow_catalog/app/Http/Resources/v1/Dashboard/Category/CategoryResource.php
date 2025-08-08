<?php

namespace App\Http\Resources\v1\Dashboard\Category;

use App\Enums\CategoryTypes;
use App\Http\Resources\v1\App\Image\ImageResource;
use App\Http\Resources\v1\MediaResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Category $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'parent_name' => $model->parent?->name,
            'name' => $model->name,
            'active' => $model->active,
            'image' => new MediaResource($model->getFirstMedia()),
            'updated_at' => $model->updated_at,
            'updated_at_timestamp' => Carbon::parse($model->updated_at)->timestamp,
        ];
    }
}
