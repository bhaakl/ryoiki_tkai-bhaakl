<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Models\MainPageProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainPageProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MainPageProduct $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'product_id' => $model->product_id,
            'title' => $model->title,
            'is_active' => $model->is_active,
        ];
    }
}
