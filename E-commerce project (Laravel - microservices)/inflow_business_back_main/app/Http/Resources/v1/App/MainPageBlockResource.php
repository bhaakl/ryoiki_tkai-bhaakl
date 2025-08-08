<?php

namespace App\Http\Resources\v1\App;

use App\Enums\MainPageTemplates;
use App\Models\MainPageBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainPageBlockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MainPageBlock $model */
        $model = $this->resource;

        $fields = [];
        $template_fields = $model->template->fields();
        foreach ($template_fields as $field) {
            $fields[$field] = $model->content[$field];
        }

        return [
            $fields,
            $this->mergeWhen($model->template == MainPageTemplates::banners, [
                'image' => new MediaResource($model->getFirstMedia('single')),
            ])
        ];
    }
}
