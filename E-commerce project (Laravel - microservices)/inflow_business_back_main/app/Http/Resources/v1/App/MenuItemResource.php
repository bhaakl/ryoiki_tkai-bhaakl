<?php

namespace App\Http\Resources\v1\App;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MenuItem $model */
        $model = $this->resource;

        return [
            'type' => $model->value,
            'request' => $model->getRequest(),
            'title' => $model->title,
            'icon' => $model->icon?->url()
        ];
    }
}
