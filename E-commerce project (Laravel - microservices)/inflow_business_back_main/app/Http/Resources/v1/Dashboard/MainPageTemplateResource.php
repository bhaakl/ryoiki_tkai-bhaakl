<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Enums\MainPageTemplates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainPageTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MainPageTemplates $model */
        $model = $this->resource;

        return [
            'name' => $model->name,
            'name_ru' => $model->toString()
        ];
    }
}
