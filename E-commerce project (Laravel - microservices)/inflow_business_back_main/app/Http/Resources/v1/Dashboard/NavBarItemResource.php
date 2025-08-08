<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Models\NavBarItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavBarItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var NavBarItem $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'value' => $model->value,
            'position' => $model->position,
            'active' => $model->active,
            'switchable' => $model->switchable,
            'icon' => $model->getIconUrl(),
        ];
    }
}
