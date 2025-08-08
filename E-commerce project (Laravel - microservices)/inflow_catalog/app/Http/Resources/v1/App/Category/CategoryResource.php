<?php

namespace App\Http\Resources\v1\App\Category;

use App\Enums\CategoryTypes;
use App\Http\Resources\v1\App\Image\ImageResource;
use App\Http\Resources\v1\MediaResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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

        $products_count = $model->products()->count();

        if ($products_count > 0) {
            $type = CategoryTypes::PRODUCTS;
        } else {
            $type = CategoryTypes::CATALOG;
        }

        return [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'name' => $model->name,
            'type' => $type,
            'image' => new MediaResource($model->getFirstMedia())
        ];
    }
}
