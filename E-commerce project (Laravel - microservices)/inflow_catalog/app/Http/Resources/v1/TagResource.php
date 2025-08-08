<?php

namespace App\Http\Resources\v1;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Tag $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'value' => $model->value,
        ];
    }
}
