<?php

namespace App\Http\Resources\v1\Dashboard\Category;

use App\Enums\CategoryTypes;
use App\Http\Resources\v1\App\Image\ImageResource;
use App\Http\Resources\v1\MediaResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryDropoutResource extends JsonResource
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
            'name' => $model->name,
        ];
    }
}
