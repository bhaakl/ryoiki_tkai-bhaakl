<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChiefOrderService
{
    public const version = 'v1';
    public $url;
    public $api_url;
    protected Tenant $tenant;
    protected $http;

    public function __construct()
    {
        $this->url = config('app.catalog_url');
        $this->api_url = $this->url . '/api/' . self::version . '/dashboard';
        $this->tenant = auth()->user()->tenant;
        $this->http = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ]);
    }

    public function index(Request $request)
    {
        $response = $this->http->get($this->api_url . '/orders', $request->input());

        return $response;
    }

    public function show($id)
    {
        $response = $this->http->get($this->api_url . "/orders/$id");

        return $response;
    }

    public function update($id, Request $request)
    {
        $response = $this->http->put($this->api_url . "/orders/$id", $request->input());

        return $response;
    }

    public function updateStatus($id, Request $request)
    {
        $response = $this->http->patch($this->api_url . "/orders/$id", $request->input());

        return $response;
    }

    public function createItem($id, Request $request)
    {
        $response = $this->http->post($this->api_url . "/orders/$id/item", $request->input());

        return $response;
    }

    public function updateItem($id, $item, Request $request)
    {
        $response = $this->http->patch($this->api_url . "/orders/$id/item/$item", $request->input());

        return $response;
    }

    public function deleteItem($id, $item)
    {
        $response = $this->http->delete($this->api_url . "/orders/$id/item/$item");

        return $response;
    }
}
