<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoyaltyService
{
    protected $base_url;
    protected $version = 'v1';
    protected AppSetting $appSetting;
    protected Tenant $tenant;

    public function __construct($version = 'v1')
    {
        $this->base_url = config('loyalty.url');
        $this->version = $version;
        $this->appSetting = AppSetting::first();
        $this->tenant = app('currentTenant');
    }

    public function register()
    {
        $response = Http::post($this->base_url . '/' . $this->version . "/register", [
            'uuid' => $this->tenant->uuid,
            'database' => $this->tenant->database,
            'loyalty_type' => $this->appSetting->loyalty_type,
        ]);

        return $response;
    }

    public function updateLoyaltyType(string $uuid, string $type)
    {
        $url = config('loyalty.url') . '/' . $this->version . '/orders';
        Http::withHeader('tenant-uuid', $uuid)->post($url, ['type' => $type]);
    }

    public function getBonusSetting()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->appSetting->loyalty_uuid,
        ])->get(config('loyalty.url') . "/" . $this->version . "/settings/bonus/main");
        $bonus_settings = $response->successful() ? $response->object() : null;

        return $bonus_settings;
    }

    public function getBonusLevels()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->appSetting->loyalty_uuid,
        ])->get(config('loyalty.url') . "/" . $this->version . "/settings/bonus/level");
        $bonus_levels = $response->successful() ? $response->object() : null;

        return $bonus_levels;
    }

    public function updateBonusSettings($request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->appSetting->loyalty_uuid,
        ])->put(config('loyalty.url') . "/" . $this->version . "/settings/bonus/main", $request->input());

        return $response;
    }

    public function createBonusLevel($request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->appSetting->loyalty_uuid,
        ])->post(config('loyalty.url') . "/" . $this->version . "/settings/bonus/level", $request->input());

        return $response;
    }

    public function updateBonusLevel($id, $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->appSetting->loyalty_uuid,
        ])->put(config('loyalty.url') . "/" . $this->version . "/settings/bonus/level/$id", $request->input());

        return $response;
    }

    public function deleteBonusLevel($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->appSetting->loyalty_uuid,
        ])->delete(config('loyalty.url') . "/" . $this->version . "/settings/bonus/level/$id");

        return $response;
    }

    public function addBonusesToCustomer($id, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->appSetting->loyalty_uuid,
        ])->post(config('loyalty.url') . "/" . $this->version . "/customers/$id/bonus-add", $request->input());

        return $response;
    }
}
