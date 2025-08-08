<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Enums\AboutTemplates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var AboutTemplates $model */
        $model = $this->resource;

        return [
            'name' => $model->name,
        ];
    }
}
