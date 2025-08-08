<?php

namespace App\Http\Controllers\Api\v1\App;

use Dadata\DadataClient;
use Illuminate\Http\Request;

class DeliveryController extends AppController
{
    public function index(Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/deliveries", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function getStores($id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/deliveries/$id/stores");

        return api_response($response->json(), $response->status());
    }

    public function getCalendar($id, Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/deliveries/$id/calendar", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function addressHint(Request $request)
    {
        $dadata = new DadataClient(config('dadata.token'), config('dadata.secret'));
        $address = $dadata->suggest('address', $request->address);

        return api_response($address);
    }
}
