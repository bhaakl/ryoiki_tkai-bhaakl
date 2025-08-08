<?php

namespace App\Http\Resources\v1\App\Kit;

use App\Models\Kit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Kit $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'uid' => $model->uid,
            'name' => $model->name,
            'items' => KitItemResource::collection($model->items)
        ];
    }
}
