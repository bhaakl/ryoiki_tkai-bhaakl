<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreController extends AppController
{
    public function __invoke()
    {
        $response = $this->http->get($this->catalogService->api_url . "/stores");

        return api_response($response->json(), $response->status());
    }
}
