<?php

namespace App\Http\Resources\v1\App\Kit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class KitCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $this->collection = KitResource::collection($this->collection);

        return parent::toArray($request);
    }

    public function paginationInformation($request)
    {
        $paginated = $this->resource->toArray();

        return [
            'pagination' => [
                'current_page' => $paginated['current_page'],
                'per_page' => $paginated['per_page'],
                'total' => $paginated['total'],
                'total_pages' => $paginated['last_page'],
            ]
        ];
    }
}
