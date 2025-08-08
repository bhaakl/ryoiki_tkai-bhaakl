<?php

namespace App\Http\Resources\v1\App\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StoreCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $this->collection = StoreResource::collection($this->collection);

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
