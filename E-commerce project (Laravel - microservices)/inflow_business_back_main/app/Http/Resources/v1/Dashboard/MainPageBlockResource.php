<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Enums\MainPageTemplates;
use App\Models\MainPageBlock;
use App\Models\MainPageProduct;
use App\Models\Promo;
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
        switch ($model->template) {
            case MainPageTemplates::promo:
                $children = Promo::whereMainPageBlockId($model->id)->get();
                $children = PromoResource::collection($children);
                break;
            case MainPageTemplates::products:
                $children = MainPageProduct::whereMainPageBlockId($model->id)->get();
                $children = MainPageProductResource::collection($children);
                break;
            case MainPageTemplates::articles:
                $children = null;
                break;
        }

        return [
            'id' => $model->id,
            'template' => $model->template->name,
            'title' => $model->title,
            'is_active' => $model->is_active,
            'sort' => $model->sort,
            'children' => $this->when(isset($children), $children ?? null)
        ];
    }
}
