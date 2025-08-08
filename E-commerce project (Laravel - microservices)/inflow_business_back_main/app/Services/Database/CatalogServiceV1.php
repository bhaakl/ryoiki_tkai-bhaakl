<?php

namespace App\Services\Database;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class CatalogServiceV1
{
    public $url;
    public $api_url;
    public const version = 'v1';
    protected Tenant $tenant;

    public function __construct()
    {
        $this->url = config('app.catalog_url');
        $this->api_url = $this->url . '/api/' . self::version;
        $this->tenant = app('currentTenant');
    }

    public function getOrderStatusCodes(): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/order-statuses/codes');

        return $response;
    }

    public function getOrderStatuses(Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/order-statuses', $request->input());

        return $response;
    }

    public function createOrderStatus(array $data)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->post($this->api_url . '/order-statuses', $data);

        return $response;
    }

    public function updateOrderStatus(int $id, array $data)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->put($this->api_url . "/order-statuses/$id", $data);

        return $response;
    }

    public function deleteOrderStatus($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->delete($this->api_url . "/order-statuses/$id");

        return $response;
    }
}
