<?php

namespace App\Http\Resources\v1\App;

use App\Models\PaymentSystem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentSystemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var PaymentSystem $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'type' => $model->type,
            'name' => $model->name,
            'description' => $model->description,
            'icon' => $model->icon->icon(),
        ];
    }
}
