<?php

namespace App\Http\Resources\v1\Dashboard\Property;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Property $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'title' => $model->title,
            'name' => $model->name,
            'type' => $model->type,
            'values' => $this->when($model->type == 'enum', $model->property_enums)
        ];
    }
}
