<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Http\Resources\v1\App\MediaResource;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChiefResource extends JsonResource
{
    public function __construct($chiefInfo)
    {
        $this->chiefInfo = $chiefInfo;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $model = $this->resource;
        return [
            'name' => $this->model->name,
            'company' => $model->tenant ? [
                'name' => $model->tenant->name,
            ] : null,
        ];
    }
}
