<?php

namespace App\Http\Resources\v1\App\Product;

use App\Enums\ProductTabTypes;
use App\Http\Resources\v1\MediaResource;
use App\Http\Resources\v1\TagResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Product $model */
        $model = $this->resource;

        $tabs = [];
        $description = $model->description ?? $model->parent?->description;
        if ($description) {
            $tab = new \stdClass();
            $tab->type = ProductTabTypes::HTML_TEXT;
            $tab->request = 'description';
            $tab->title = 'Описание';
            $tabs[] = $tab;
        }
        if ($model->property_enums()->exists() || $model->property_strings()->exists()) {
            $tab = new \stdClass();
            $tab->type = ProductTabTypes::CHARACTERISTICS;
            $tab->request = 'characteristics';
            $tab->title = 'Характеристики';
            $tabs[] = $tab;
        }

        if ($model->parent_id && $model->components()->count() == 0) {
            $components = $model->parent->components;
        } else {
            $components = $model->components;
        }
        if (count($components) > 0) {
            $tab = new \stdClass();
            $tab->type = ProductTabTypes::COMPONENTS;
            $tab->request = 'components';
            $tab->title = 'Состав';
            $tabs[] = $tab;
        }

        return [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'parent_name' => $this->when($model->parent_id, $model->parent?->title),
            'article' => $model->article,
            'title' => $model->title ?? $model->parent?->title,
            'price' => $this->when($model->parent_id, $model->price),
            'sale_price' => $this->when($model->parent_id, $model->promo_price),
            'badges' => $this->when($model->parent_id && $model->discount, BadgeResource::collection([collect(['text' => $model->discount])])),
            'by_order' => $this->when($model->parent_id, $model->by_order),
            'has_package' => $this->when($model->parent_id, $model->has_package),
            'max_amount' => $this->when($model->parent_id, 999),
            'tags' => $this->when(!$model->parent_id, TagResource::collection($model->tags)),
            'photos' => MediaResource::collection($model->parent_id ? $model->parent?->getMedia('*') : $model->getMedia('*')),
            'variants' => $this->when(!$model->parent_id, ProductDetailResource::collection($model->offers)),
            'tabs' => $tabs
        ];
    }
}
