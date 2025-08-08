<?php

namespace App\Http\Resources\v1\App\Property;

use App\Models\PropertyEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyEnumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var PropertyEnum $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'value' => $model->value,
        ];
    }
}
