<?php

namespace App\Http\Resources\v1\Dashboard\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChiefOrderStatusCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->collection = ChiefOrderStatusResource::collection($this->collection);

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
