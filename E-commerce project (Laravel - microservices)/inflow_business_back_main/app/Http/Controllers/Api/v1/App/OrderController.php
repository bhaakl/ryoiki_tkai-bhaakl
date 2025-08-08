<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Enums\AuthTypes;
use App\Events\OrderCreatedEvent;
use App\Events\OrderUpdatedEvent;
use App\Http\Requests\v1\App\OrderOneClickRequest;
use App\Http\Requests\v1\App\OrderStoreRequest;
use App\Models\AppSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends AppController
{
    public function __construct(protected AppSetting $appSetting)
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/orders", $request->input());

        return api_response($response->json(), $response->status());
    }

    public function show($id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/orders/$id");

        return api_response($response->json(), $response->status());
    }

    public function store(OrderStoreRequest $request)
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $response = $this->http->post($this->catalogService->api_url . "/orders/create", array_merge($request->validated(), [
            'user_id' => $customer->id,
            'user_name' => $customer->name,
            'user_phone' => $customer->phone,
            'user_email' => $customer->email,
            'max_bonus' => $customer->getBonusBalance()
        ]));

        if ($response->successful()) {
            Log::debug('order created');
            event(new OrderCreatedEvent($customer, $response->object()));
        }

        return api_response($response->json(), $response->status());
    }

    public function oneClick(OrderOneClickRequest $request)
    {
        if (!auth('customer')->check()) {
            /** @var AppSetting $setting */
            $setting = AppSetting::first();
            if ($setting->auth_type == AuthTypes::Phone) {
                $customer = Customer::wherePhone($request->user_phone)->first();
            } else {
                $customer = Customer::whereEmail($request->user_email)->first();
            }
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $request->user_name,
                    'phone' => $request->user_phone,
                    'email' => $request->user_email,
                ]);
            }
            $this->http->withHeader('user', $customer->id);
        }

        $response = $this->http->post($this->catalogService->api_url . "/orders/one-click", $request->input());
        if ($response->successful()) {
            Log::debug('order created');
            event(new OrderCreatedEvent($customer, $response->object()));
        }

        return api_response($response->json(), $response->status());
    }

    public function cancel($id)
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $response = $this->http->get($this->catalogService->api_url . "/orders/$id/cancel");

        if ($response->failed()) {
            return api_response($response->json(), $response->status());
        }

        Log::debug('order canceled');
        event(new OrderUpdatedEvent($customer, $response->object()));

        return api_response($response->json(), $response->status());
    }

    public function orderStatusList()
    {
        $response = $this->http->get($this->catalogService->api_url . "/orders/statuses");

        return api_response($response->json(), $response->status());
    }
}
