<?php

namespace App\Http\Resources\v1\App\Store;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Store $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'name' => $model->name ?? $model->address,
            'address' => $model->address,
            'phone' => $model->phone,
            'subway' => $model->subway,
            'lon' => $model->lon,
            'lat' => $model->lat,
            'open' => $model->open,
            'pickup' => $model->pickup,
            'shop' => $model->shop,
            'active' => $model->active,
        ];
    }
}
