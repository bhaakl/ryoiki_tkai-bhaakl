<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Enums\AboutTemplates;
use App\Models\AboutItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var AboutItem $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'type' => $model->type->name,
            'title' => $model->title,
        ];
    }
}
