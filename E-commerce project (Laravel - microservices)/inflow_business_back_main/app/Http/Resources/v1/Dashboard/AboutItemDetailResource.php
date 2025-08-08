<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Enums\AboutTemplates;
use App\Models\AboutItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutItemDetailResource extends JsonResource
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
            'text' => $this->when(in_array($model->type,[AboutTemplates::html_text]), $model->text),
            'image' => $this->when($model->type == AboutTemplates::license, new MediaCollection($model->media()->paginate($request->per_page ?? 15))),
            'images' => $this->when($model->type == AboutTemplates::photo, new MediaCollection($model->media()->paginate($request->per_page ?? 15))),
        ];
    }
}
