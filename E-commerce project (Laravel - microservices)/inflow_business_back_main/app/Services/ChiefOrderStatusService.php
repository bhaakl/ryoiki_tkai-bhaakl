<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class ChiefOrderStatusService
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

    public function getOrderStatusCodes(): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $response = $this->http->get($this->api_url . '/order-statuses/codes');

        return $response;
    }

    public function getOrderStatuses(Request $request)
    {
        $response = $this->http->get($this->api_url . '/order-statuses', $request->input());

        return $response;
    }

    public function getOrderStatusesDropout()
    {
        $response = $this->http->get($this->api_url . '/order-statuses/dropout');

        return $response;
    }

    public function createOrderStatus(array $data)
    {
        $response = $this->http->post($this->api_url . '/order-statuses', $data);

        return $response;
    }

    public function updateOrderStatus(int $id, array $data)
    {
        $response = $this->http->put($this->api_url . "/order-statuses/$id", $data);

        return $response;
    }

    public function deleteOrderStatus($id)
    {
        $response = $this->http->delete($this->api_url . "/order-statuses/$id");

        return $response;
    }
}
