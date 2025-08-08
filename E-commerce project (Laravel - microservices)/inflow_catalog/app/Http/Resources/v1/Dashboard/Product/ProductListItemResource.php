<?php

namespace App\Http\Resources\v1\Dashboard\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListItemResource extends JsonResource
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
            'title' => $model->title,
        ];
    }
}
