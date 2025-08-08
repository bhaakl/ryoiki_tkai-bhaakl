<?php

namespace App\Http\Resources\v1\App\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BadgeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            //'color' => '{{primary}}',
            'color' => '#FFFFFFFF',
            'text' => $this['text'],
            'textColor' => '{{text}}'
        ];
    }
}
