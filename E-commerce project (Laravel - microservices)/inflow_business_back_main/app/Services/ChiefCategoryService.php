<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChiefCategoryService
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

    public function index(Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/categories', $request->input());

        return $response;
    }

    public function dropout(Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . '/categories/dropout', $request->input());

        return $response;
    }

    public function create(Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $response = $response->attach('image', file_get_contents($image), $image->getClientOriginalName());
        }

        $response = $response->post($this->api_url . '/categories', $request->input());

        return $response;
    }

    public function show($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->get($this->api_url . "/categories/$id");

        return $response;
    }

    public function update($id, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->put($this->api_url . "/categories/$id", $request->input());

        return $response;
    }

    public function uploadImage($id, Request $request)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $response = $response->attach('image', file_get_contents($image), $image->getClientOriginalName());
        }

        $response = $response->post($this->api_url . "/categories/$id", $request->input());

        return $response;
    }

    public function deleteImage($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->delete($this->api_url . "/categories/$id/image");

        return $response;
    }

    public function delete($id)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'tenant-uuid' => $this->tenant->uuid,
        ])->delete($this->api_url . "/categories/$id");

        return $response;
    }
}
