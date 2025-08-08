<?php

namespace App\Http\Resources\v1\App;

use App\Models\About;
use App\Models\AboutItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var About $model */
        $model = $this->resource;

        $items = AboutItem::all();

        return [
            'image' => new MediaResource($model->getFirstMedia()),
            'tabs' => AboutItemResource::collection($items),
        ];
    }
}
