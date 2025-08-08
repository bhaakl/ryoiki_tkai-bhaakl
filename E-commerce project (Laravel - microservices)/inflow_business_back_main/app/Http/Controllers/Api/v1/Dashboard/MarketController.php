<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\SwitchMarketRequest;
use App\Http\Resources\v1\Dashboard\AppConfigResource;
use App\Models\AppSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ChiefOrderStatusService;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    protected Tenant $tenant;
    protected AppSetting $appSetting;

    public function __construct(protected ChiefOrderStatusService  $orderStatusService)
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
        $this->appSetting = AppSetting::first();
    }

    public function switchMarket(SwitchMarketRequest $request)
    {
        $this->appSetting->update(['has_market' => $request->active]);

        return api_response(new AppConfigResource($this->appSetting->refresh()));
    }

    public function getCodes()
    {
        $response = $this->orderStatusService->getOrderStatusCodes();

        return api_response($response->object(), $response->status());
    }

    public function index(Request $request)
    {
        $response = $this->orderStatusService->getOrderStatuses($request);

        return api_response($response->object(), $response->status());
    }

    public function dropout()
    {
        $response = $this->orderStatusService->getOrderStatusesDropout();

        return api_response($response->object(), $response->status());
    }

    public function store(Request $request)
    {
        $response = $this->orderStatusService->createOrderStatus($request->input());

        return api_response($response->object(), $response->status());
    }

    public function update($id, Request $request)
    {
        $response = $this->orderStatusService->updateOrderStatus($id, $request->input());

        return api_response($response->object(), $response->status());
    }

    public function destroy($id)
    {
        $response = $this->orderStatusService->deleteOrderStatus($id);

        return api_response($response->object(), $response->status());
    }
}
