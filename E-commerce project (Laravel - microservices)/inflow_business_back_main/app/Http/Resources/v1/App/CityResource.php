<?php

namespace App\Http\Resources\v1\App;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var City $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'name' => $model->name
        ];
    }
}
