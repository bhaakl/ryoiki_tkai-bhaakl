<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Media extends JsonResource
{
    /**
     * Преобразование ресурса в массив.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => url($this->getUrl()),
            'thumb_url' => url($this->getUrl('thumb')),
        ];
    }
}
