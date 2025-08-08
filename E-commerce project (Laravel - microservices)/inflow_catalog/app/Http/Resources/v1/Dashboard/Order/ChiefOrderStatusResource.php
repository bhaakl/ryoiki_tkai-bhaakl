<?php

namespace App\Http\Resources\v1\Dashboard\Order;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChiefOrderStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var OrderStatus $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'ext_id' => $model->ext_id,
            'code' => $model->code,
            'name' => $model->name,
            'active' => $model->active,
        ];
    }
}
