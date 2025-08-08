<?php

namespace App\Http\Resources\v1;

use App\Enums\MeasurementUnits;
use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComponentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Component $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'name' => $model->name,
            'quantity' => $this->whenPivotLoaded('component_product', function () use ($model) {
                return $model->pivot->quantity;
            }),
            'unit' => $this->whenPivotLoaded('component_product', function () use ($model) {
                return $model->pivot->unit;
            }),
            'unit_name' => $this->whenPivotLoaded('component_product', function () use ($model) {
                return MeasurementUnits::from($model->pivot->unit)->name();
            }),
        ];
    }
}
