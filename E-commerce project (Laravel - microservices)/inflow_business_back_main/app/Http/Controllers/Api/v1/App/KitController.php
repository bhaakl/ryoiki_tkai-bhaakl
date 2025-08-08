<?php

namespace App\Http\Controllers\Api\v1\App;

use Illuminate\Http\Request;

class KitController extends AppController
{
    public function index(Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/kits", $request->input());

        return api_response($response->json(), $response->status());
    }
}
