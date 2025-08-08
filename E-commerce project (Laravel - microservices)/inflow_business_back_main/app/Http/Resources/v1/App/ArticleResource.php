<?php

namespace App\Http\Resources\v1\App;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Article $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'title' => $model->title,
            'description' => Str::words($model->description, 50),
            'image' => new MediaResource($model->getFirstMedia()),
            'date' => $model->created_at->timestamp
        ];
    }
}
