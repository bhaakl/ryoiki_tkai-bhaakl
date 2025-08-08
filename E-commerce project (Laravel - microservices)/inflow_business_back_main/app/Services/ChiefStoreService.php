<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChiefStoreService
{
    public const version = 'v1';
    public $url;
    public $api_url;
    protected Tenant $tenant;

    public function __construct()
    {
        $this->url = config('app.catalog_url');
        $this->api_url = $this->url . '/api/' . self::version . '/dashboard';
        $this->tenant = auth()->user()->tenant;
    }

    public function getList(Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/stores', $request->input());

        return $response;
    }

    public function create($request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->post($this->api_url . '/stores', $request->input());

        return $response;
    }

    public function show($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/stores/$id");

        return $response;
    }

    public function update($id, $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->put($this->api_url . "/stores/$id", $request->input());

        return $response;
    }

    public function delete($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->delete($this->api_url . "/stores/$id");

        return $response;
    }
}
