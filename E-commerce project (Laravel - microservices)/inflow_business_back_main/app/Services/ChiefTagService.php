<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChiefTagService
{
    public const version = 'v1';
    public $url;
    public $api_url;
    protected Tenant $tenant;
    protected $http;

    public function __construct()
    {
        $this->url = config('app.catalog_url');
        $this->api_url = $this->url . '/api/' . self::version . '/dashboard/tags';
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

    public function create(Request $request)
    {
        $response = $this->http->post($this->api_url, $request->input());

        return $response;
    }

    public function delete(int $id)
    {
        $response = $this->http->delete($this->api_url . "/$id");

        return $response;
    }
}
