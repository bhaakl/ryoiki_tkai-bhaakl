<?php

namespace App\Http\Resources\v1\App;

use App\Enums\SocialNetworks;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialLinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var SocialLink $model */
        $model = $this->resource;

        return [
            'network' => $model->network,
            'icon' => new IconResource(SocialNetworks::{$model->network}->icon()),
            'link' => $model->link,
            'android_link' => $model->android_link,
            'ios_link' => $model->ios_link,
        ];
    }
}
