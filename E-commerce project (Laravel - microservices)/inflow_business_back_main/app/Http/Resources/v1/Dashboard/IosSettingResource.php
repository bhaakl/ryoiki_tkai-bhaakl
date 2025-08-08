<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Http\Resources\v1\App\MediaResource;
use App\Models\IosSetting;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IosSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var IosSetting $model */
        $model = $this->resource;

        return [
            'title' => $model->title,
            'user_agreement_url' => $model->user_agreement_url,
            'support_url' => $model->support_url,
            'description' => $model->description,
            'key_words' => $model->key_words,
            'name' => $model->name,
            'address' => $model->address,
            'email' => $model->email,
            'phone' => $model->phone,
            'copyright' => $model->copyright,
            'app_icon' => new MediaResource($model->getFirstMedia(Media::IOS_APP_ICON_COLLECTION)),
            'iphone5_5-8' => MediaResource::collection($model->getMedia(Media::IPHONE_5_5_8_COLLECTION)),
            'iphone6_5-11' => MediaResource::collection($model->getMedia(Media::IPHONE_6_5_11_COLLECTION)),
            'iphone6_7-14' => MediaResource::collection($model->getMedia(Media::IPHONE_6_7_17_COLLECTION)),
            'ipad_pro_3' => MediaResource::collection($model->getMedia(Media::IPAD_PRO_3_COLLECTION)),
        ];
    }
}
