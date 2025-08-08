<?php

namespace App\Http\Resources\v1\App;

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
            'title' => $model->title,
            'type' => $model->type->name,
            'request' => $model->id,
            $this->mergeWhen($request->routeIs('about.item'), [
                $this->mergeWhen($model->type == AboutTemplates::html_text, [
                    'text' => $model->text
                ]),
                $this->mergeWhen(in_array($model->type, [AboutTemplates::photo, AboutTemplates::license]), [
                    'images' => MediaResource::collection($model->getMedia())
                ])
            ])
        ];
    }
}
