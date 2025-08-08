<?php

namespace App\Http\Controllers\Api\v1\App;


use App\Http\Requests\v1\App\SearchRequest;

class SearchController extends AppController
{
    public function __invoke(SearchRequest $request)
    {
        if (!$request->input('segment')) {
            $products = $this->http->get($this->catalogService->api_url . "/products/search", [
                'search' => $request->search
            ]);
            $products = $products->object();
            $count = count((array)$products) ?? 0;
            $products = $this->http->get($this->catalogService->api_url . "/products/search", [
                'search' => $request->search,
                'limit' => 3
            ]);
            $products = $products->object();

            return api_response([
                'products' => [
                    'count' => $count,
                    'data' => $products
                ],
                'services' => null,
                'specialists' => null
            ]);
        } elseif ($request->input('segment') == 'products') {
            $items = $this->http->get($this->catalogService->api_url . "/products/search", [
                'search' => $request->search
            ]);
            $items = $items->object();

        } elseif ($request->input('segment') == 'services') {
            $items = [];
        } else {
            $items = [];
        }

        return api_response($items);
    }
}
