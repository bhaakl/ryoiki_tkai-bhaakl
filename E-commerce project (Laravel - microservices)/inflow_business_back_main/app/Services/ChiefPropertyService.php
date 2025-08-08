<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChiefPropertyService
{
    public const version = 'v1';
    public $url;
    public $api_url;
    protected Tenant $tenant;
    protected $http;

    public function __construct()
    {
        $this->url = config('app.catalog_url');
        $this->api_url = $this->url . '/api/' . self::version . '/dashboard/properties';
        $this->tenant = auth()->user()->tenant;
        $this->http = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ]);
    }

    public function index(Request $request)
    {
        $response = $this->http->get($this->api_url, $request->input());

        return $response;
    }

    public function dropout()
    {
        $response = $this->http->get($this->api_url . '/dropout');

        return $response;
    }

    public function create(Request $request)
    {
        $response = $this->http->post($this->api_url, $request->input());

        return $response;
    }

    public function show(int $id)
    {
        $response = $this->http->get($this->api_url . "/$id");

        return $response;
    }

    public function update(int $id, Request $request)
    {
        $response = $this->http->put($this->api_url . "/$id", $request->input());

        return $response;
    }

    public function delete(int $id)
    {
        $response = $this->http->delete($this->api_url . "/$id");

        return $response;
    }

    public function getEnums(int $id)
    {
        $response = $this->http->get($this->api_url . "/$id/enums");

        return $response;
    }

    public function addEnum(Request $request)
    {
        $response = $this->http->post($this->api_url . "/enums", $request->input());

        return $response;
    }

    public function deleteEnum(int $id)
    {
        $response = $this->http->delete($this->api_url . "/enums/$id");

        return $response;
    }

    public function getStrings(Request $request)
    {
        $response = $this->http->delete($this->api_url . "strings", $request->input());

        return $response;
    }
}
