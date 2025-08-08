<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChiefAcquiringService
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

    public function show()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/acquiring');

        return $response;
    }

    public function update($id, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->put($this->api_url . "/acquiring/$id", $request->input());

        return $response;
    }
}
