<?php

namespace App\Http\Controllers\Api\v1\App;

use Illuminate\Http\Request;

class CategoryController extends AppController
{
    public function index(Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/categories", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function show(Request $request, $id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/categories/$id", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function showDetail($id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/categories/$id/detail");

        return api_response($response->json(), $response->status());
    }
}
