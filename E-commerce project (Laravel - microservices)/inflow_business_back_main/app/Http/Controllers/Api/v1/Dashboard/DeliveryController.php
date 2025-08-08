<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ChiefDeliveryService;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct(private ChiefDeliveryService $deliveryService)
    {
    }

    public function getTypes()
    {
        $response = $this->deliveryService->getDeliveryTypes();

        return api_response($response->object(), $response->status());
    }

    public function getIcons()
    {
        $response = $this->deliveryService->getDeliveryIcons();

        return api_response($response->object(), $response->status());
    }

    public function index(Request $request)
    {
        $response = $this->deliveryService->getList($request);

        return api_response($response->object(), $response->status());
    }

    public function store(Request $request)
    {
        $response = $this->deliveryService->create($request);

        return api_response($response->object(), $response->status());
    }

    public function show($id)
    {
        $response = $this->deliveryService->show($id);

        return api_response($response->object(), $response->status());
    }

    public function update($id, Request $request)
    {
        $response = $this->deliveryService->update($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function destroy($id)
    {
        $response = $this->deliveryService->delete($id);

        return api_response($response->object(), $response->status());
    }

    public function getAvailableStores($delivery)
    {
        $response = $this->deliveryService->getAvailableStores($delivery);

        return api_response($response->object(), $response->status());
    }

    public function attachStore($delivery, $store)
    {
        $response = $this->deliveryService->attachStore($delivery, $store);

        return api_response($response->object(), $response->status());
    }

    public function detachStore($delivery, $store)
    {
        $response = $this->deliveryService->detachStore($delivery, $store);

        return api_response($response->object(), $response->status());
    }

    public function attachInterval($delivery, Request $request)
    {
        $response = $this->deliveryService->attachInterval($delivery, $request);

        return api_response($response->object(), $response->status());
    }

    public function detachInterval($delivery, $interval)
    {
        $response = $this->deliveryService->detachInterval($delivery, $interval);

        return api_response($response->object(), $response->status());
    }

    public function attachCondition($delivery, Request $request)
    {
        $response = $this->deliveryService->attachCondition($delivery, $request);

        return api_response($response->object(), $response->status());
    }

    public function updateCondition($delivery, $condition, Request $request)
    {
        $response = $this->deliveryService->updateCondition($delivery, $condition, $request);

        return api_response($response->object(), $response->status());
    }

    public function detachCondition($delivery, $condition)
    {
        $response = $this->deliveryService->detachCondition($delivery, $condition);

        return api_response($response->object(), $response->status());
    }

    public function attachRestriction($delivery, Request $request)
    {
        $response = $this->deliveryService->attachRestriction($delivery, $request);

        return api_response($response->object(), $response->status());
    }

    public function updateRestriction($delivery, $restriction, Request $request)
    {
        $response = $this->deliveryService->updateRestriction($delivery, $restriction, $request);

        return api_response($response->object(), $response->status());
    }

    public function detachRestriction($delivery, $restriction)
    {
        $response = $this->deliveryService->detachRestriction($delivery, $restriction);

        return api_response($response->object(), $response->status());
    }
}
