<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ChiefProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ChiefProductService $productService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->productService->index($request);

        return api_response($response->object(), $response->status());
    }

    public function list(Request $request)
    {
        $response = $this->productService->list($request);

        return api_response($response->object(), $response->status());
    }

    public function store(Request $request)
    {
        $response = $this->productService->create($request);

        return api_response($response->object(), $response->status());
    }

    public function show($id)
    {
        $response = $this->productService->show($id);

        return api_response($response->object(), $response->status());
    }

    public function update(Request $request, $id)
    {
        $response = $this->productService->update($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function destroy($id)
    {
        $response = $this->productService->delete($id);

        return api_response($response->object(), $response->status());
    }

    public function uploadImage($id, Request $request)
    {
        $response = $this->productService->uploadImage($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function deleteImage($id, $uuid)
    {
        $response = $this->productService->deleteImage($id, $uuid);

        return api_response($response->object(), $response->status());
    }

    public function offers($id, Request $request)
    {
        $response = $this->productService->showOffers($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function storeOffer($id, Request $request)
    {
        $response = $this->productService->createOffer($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function showOffer($id, $offer)
    {
        $response = $this->productService->showOffer($id, $offer);

        return api_response($response->object(), $response->status());
    }

    public function updateOffer($id, $offer, Request $request)
    {
        $response = $this->productService->updateOffer($id, $offer, $request);

        return api_response($response->object(), $response->status());
    }

    public function destroyOffer($id, $offer)
    {
        $response = $this->productService->deleteOffer($id, $offer);

        return api_response($response->object(), $response->status());
    }

    public function updateProperties($id, Request $request)
    {
        $response = $this->productService->updateProperties($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function removeProperty($id, Request $request)
    {
        $response = $this->productService->removeProperty($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function attachTag($id, $tag)
    {
        $response = $this->productService->attachTag($id, $tag);

        return api_response($response->object(), $response->status());
    }

    public function syncTags($id, Request $request)
    {
        $response = $this->productService->syncTags($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function detachTag($id, $tag)
    {
        $response = $this->productService->detachTag($id, $tag);

        return api_response($response->object(), $response->status());
    }

    public function attachComponent($id, Request $request)
    {
        $response = $this->productService->attachComponent($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function syncComponents($id, Request $request)
    {
        $response = $this->productService->syncComponents($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function detachComponent($id, $component)
    {
        $response = $this->productService->detachComponent($id, $component);

        return api_response($response->object(), $response->status());
    }

    public function showSimilar($id, Request $request)
    {
        $response = $this->productService->showSimilar($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function syncSimilar($id, Request $request)
    {
        $response = $this->productService->syncSimilar($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function detachSimilar($id, $similar)
    {
        $response = $this->productService->detachSimilar($id, $similar);

        return api_response($response->object(), $response->status());
    }
}
