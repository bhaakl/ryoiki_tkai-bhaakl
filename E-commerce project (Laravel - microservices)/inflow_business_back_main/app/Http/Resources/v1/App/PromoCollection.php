<?php

namespace App\Http\Resources\v1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class PromoCollection extends ResourceCollection
{
    private $pagination;

    public function __construct($resource)
    {
        $this->pagination = Arr::pagination($resource);
        $resource = $resource->getCollection();

        parent::__construct($resource);
    }

    public function toArray(Request $request)
    {
        if ($this->pagination) {
            return [
                'data' => PromoResource::collection($this->collection),
                'pagination' => $this->pagination
            ];
        }

        return $this->collection;
    }
}
