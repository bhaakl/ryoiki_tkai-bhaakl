<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemDetailResource extends JsonResource
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
            'id' => $model->id,
            'system_name' => $model->value->systemName(),
            'title' => $model->title,
            'renameable' => $model->value->renameable(),
            'is_active' => $model->is_active,
            'is_custom' => $model->value->isCustom(),
            'position' => $model->position,
            'content' => $model->content,
            'icon_name' => $model->icon?->name,
            'icon_url' => $model->icon?->url(),
        ];
    }
}
