<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Tenant;

class ChiefService
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

    public function chief()
    {
        try {
            $chiefInfo = ['name' => auth()->user()->name, 'phone' => auth()->user()->phone, 'email' => auth()->user()->email];
            $companyName = [
                'name' => $this->tenant->name
            ];
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'tenant-uuid' => $this->tenant->uuid,
            ])->get($this->api_url . '/metrics');
        } catch (\Exception $e) {
            return api_error($e->getMessage());
        }

        return array('chief' => $chiefInfo, 'company' => $companyName, 'data' => $response->json());
    }

}