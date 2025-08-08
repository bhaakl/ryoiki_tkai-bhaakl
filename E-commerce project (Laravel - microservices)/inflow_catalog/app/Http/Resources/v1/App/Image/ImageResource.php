<?php

namespace App\Http\Resources\v1\App\Image;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $app_url = (explode('://', config('app.url')));
        $url = $app_url[0] . '://' . $app_url[1];

        return [
            'id' => $this->id,
            'original_url' => $url . '/' . $this->original_url,
            'preview_url' => $url . '/' . $this->preview_url,
        ];
    }
}
