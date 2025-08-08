<?php

namespace App\Http\Controllers\Api\v1\App;

use Illuminate\Http\Request;

class ProductController extends AppController
{
    public function index(Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/products", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function refresh(Request $request)
    {
        $response = $this->http->post($this->catalogService->api_url . "/products/refresh", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function show(Request $request, $id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/products/$id", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function showDescription(Request $request, $id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/products/$id/description", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function showCharacteristics(Request $request, $id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/products/$id/characteristics", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function showComponents($id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/products/$id/components");

        return api_response($response->json(), $response->status());
    }

    public function showSimilar(Request $request, $id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/products/$id/similar", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function getFilters(Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/products/filters", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function getProperty($id, Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/products/properties/$id", $request->input());

        return api_response($response->json(), $response->status());
    }
}
