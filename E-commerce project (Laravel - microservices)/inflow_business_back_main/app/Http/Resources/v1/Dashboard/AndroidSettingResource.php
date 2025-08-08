<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Http\Resources\v1\App\MediaResource;
use App\Models\AndroidSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AndroidSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var AndroidSetting $model */
        $model = $this->resource;

        return [
            'title' => $model->title,
            'short_description' => $model->short_description,
            'description' => $model->description,
            'user_agreement_url' => $model->user_agreement_url,
            'user_delete_form_url' => $model->user_delete_form_url,
            'default_language' => $model->default_language,
            'app_category' => $model->app_category,
            'android_app_icon' => MediaResource::collection($model->getMedia('android_app_icon')),
            'market_banner' => MediaResource::collection($model->getMedia('market_banner')),
            'market_image' => MediaResource::collection($model->getMedia('market_image')),
        ];
    }
}
