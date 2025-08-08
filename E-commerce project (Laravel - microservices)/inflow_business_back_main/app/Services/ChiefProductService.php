<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChiefProductService
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

    public function index(Request $request)
    {
        $response = $this->http->get($this->api_url . '/products', $request->input());

        return $response;
    }

    public function list(Request $request)
    {
        $response = $this->http->get($this->api_url . '/products/list', $request->input());

        return $response;
    }

    public function create(Request $request)
    {
        $response = $this->http->post($this->api_url . '/products', $request->input());

        return $response;
    }

    public function show($id)
    {
        $response = $this->http->get($this->api_url . "/products/$id");

        return $response;
    }

    public function update($id, Request $request)
    {
        $response = $this->http->put($this->api_url . "/products/$id", $request->input());

        return $response;
    }

    public function uploadImage($id, Request $request)
    {
        $response = $this->http;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $response = $response->attach('image', file_get_contents($image), $image->getClientOriginalName());
        }

        $response = $response->post($this->api_url . "/products/$id/image", $request->input());

        return $response;
    }

    public function deleteImage($id, $uuid)
    {
        $response = $this->http->delete($this->api_url . "/products/$id/image/$uuid");

        return $response;
    }

    public function delete($id)
    {
        $response = $this->http->delete($this->api_url . "/products/$id");

        return $response;
    }

    public function showOffers($id, Request $request)
    {
        $response = $this->http->get($this->api_url . "/products/$id/offers", $request->input());

        return $response;
    }

    public function createOffer($id, $request)
    {
        $response = $this->http->post($this->api_url . "/products/$id/offers", $request->input());

        return $response;
    }

    public function showOffer($id, $offer_id)
    {
        $response = $this->http->get($this->api_url . "/products/$id/offers/$offer_id");

        return $response;
    }

    public function updateOffer($id, $offer_id, $request)
    {
        $response = $this->http->put($this->api_url . "/products/$id/offers/$offer_id", $request->input());

        return $response;
    }

    public function deleteOffer($id, $offer_id)
    {
        $response = $this->http->delete($this->api_url . "/products/$id/offers/$offer_id");

        return $response;
    }

    public function updateProperties($id, $request)
    {
        $response = $this->http->put($this->api_url . "/products/$id/properties", $request->input());

        return $response;
    }

    public function removeProperty($id, $request)
    {
        $response = $this->http->delete($this->api_url . "/products/$id/properties", $request->input());

        return $response;
    }

    public function attachTag($id, $tag)
    {
        $response = $this->http->post($this->api_url . "/products/$id/tags/$tag");

        return $response;
    }

    public function syncTags($id, Request $request)
    {
        $response = $this->http->put($this->api_url . "/products/$id/tags", $request->input());

        return $response;
    }

    public function detachTag($id, $tag)
    {
        $response = $this->http->delete($this->api_url . "/products/$id/tags/$tag");

        return $response;
    }

    public function attachComponent($id, Request $request)
    {
        $response = $this->http->post($this->api_url . "/products/$id/components", $request->input());

        return $response;
    }

    public function syncComponents($id, Request $request)
    {
        $response = $this->http->put($this->api_url . "/products/$id/components", $request->input());

        return $response;
    }

    public function detachComponent($id, $component)
    {
        $response = $this->http->delete($this->api_url . "/products/$id/components/$component");

        return $response;
    }

    public function showSimilar($id, Request $request)
    {
        $response = $this->http->get($this->api_url . "/products/$id/similar", $request->input());

        return $response;
    }

    public function syncSimilar($id, Request $request)
    {
        $response = $this->http->put($this->api_url . "/products/$id/similar", $request->input());

        return $response;
    }

    public function detachSimilar($id, $similar)
    {
        $response = $this->http->delete($this->api_url . "/products/$id/similar/$similar");

        return $response;
    }
}
