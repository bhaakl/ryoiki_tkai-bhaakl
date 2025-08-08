<?php

namespace App\Http\Resources\v1\App;

use App\Models\NavBarItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavBarItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): string
    {
        /** @var NavBarItem $model */
        $model = $this->resource;

        return $model->value;
    }
}
