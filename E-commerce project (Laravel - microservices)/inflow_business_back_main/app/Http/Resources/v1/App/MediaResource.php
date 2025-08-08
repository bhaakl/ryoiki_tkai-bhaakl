<?php

namespace App\Http\Resources\v1\App;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $props = [];
        foreach ($this->custom_properties as $property_name => $property_value) {
            $props[$property_name] = $property_value;
        }

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'original_url' => $this->original_url,
            'preview_url' => $this->hasGeneratedConversion('preview') ? $this->getUrl('preview') : null,
            'collection_name' => $this->collection_name,
            'url' => $this->getUrl(),
            $this->mergeWhen(count($props) > 0, $props),
        ];
    }
}
