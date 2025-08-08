<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChiefDeliveryService
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

    public function getDeliveryTypes()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/deliveries/types');

        return $response;
    }

    public function getDeliveryIcons()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/deliveries/icons');

        return $response;
    }

    public function getList($request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/deliveries', $request->input());

        return $response;
    }

    public function create($request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->post($this->api_url . '/deliveries', $request->input());

        return $response;
    }

    public function show($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/deliveries/$id");

        return $response;
    }

    public function update($id, $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->put($this->api_url . "/deliveries/$id", $request->input());

        return $response;
    }

    public function delete($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->delete($this->api_url . "/deliveries/$id");

        return $response;
    }

    public function getAvailableStores($delivery)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/deliveries/$delivery/stores");

        return $response;
    }

    public function attachStore($delivery, $store)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/deliveries/$delivery/stores/$store/attach");

        return $response;
    }

    public function detachStore($delivery, $store)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/deliveries/$delivery/stores/$store/detach");

        return $response;
    }

    public function attachInterval($delivery, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->post($this->api_url . "/deliveries/$delivery/intervals/attach", $request->input());

        return $response;
    }

    public function detachInterval($delivery, $interval)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/deliveries/$delivery/intervals/$interval/detach");

        return $response;
    }

    public function attachCondition($delivery, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->post($this->api_url . "/deliveries/$delivery/conditions/attach", $request->input());

        return $response;
    }

    public function updateCondition($delivery, $condition, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->put($this->api_url . "/deliveries/$delivery/conditions/$condition", $request->input());

        return $response;
    }

    public function detachCondition($delivery, $condition)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/deliveries/$delivery/conditions/$condition/detach");

        return $response;
    }

    public function attachRestriction($delivery, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->post($this->api_url . "/deliveries/$delivery/restrictions/attach", $request->input());

        return $response;
    }

    public function updateRestriction($delivery, $restriction, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->put($this->api_url . "/deliveries/$delivery/restrictions/$restriction", $request->input());

        return $response;
    }

    public function detachRestriction($delivery, $restriction)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/deliveries/$delivery/restrictions/$restriction/detach");

        return $response;
    }
}
