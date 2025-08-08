<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Events\OrderPaidEvent;
use App\Models\AppSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends AppController
{
    public function __construct(protected AppSetting $appSetting)
    {
        parent::__construct();
    }

    public function pay($id)
    {
        $response = $this->http->get($this->catalogService->api_url . "/payments/orders/$id/pay");

        return api_response($response->json(), $response->status());
    }

    public function payWithCryptogram($id, Request $request)
    {
        $response = $this->http->get($this->catalogService->api_url . "/payments/orders/$id/pay/cryptogram", [
            'cryptogram' => $request->get('cryptogram'),
            'ip' => $request->ip(),
        ]);

        return api_response($response->json(), $response->status());
    }

    public function getState($id)
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();

        $initial_order_state = $this->http->get($this->catalogService->api_url . "/orders/$id");
        if ($initial_order_state->failed()) {
            return api_response($initial_order_state->json(), $initial_order_state->status());
        }

        $response = $this->http->get($this->catalogService->api_url . "/payments/orders/$id/state");
        if ($response->failed()) {
            return api_response($response->json(), $response->status());
        }

        Log::debug([
            '$initial_order_state' => $initial_order_state->object()->paid,
            '$response' => $response->object()->paid
        ]);

        if ($response->object()->paid && !$initial_order_state->object()->paid) {
            Log::debug('OrderPaidEvent fire', ['order' => $response->object()]);
            event(new OrderPaidEvent($customer, $response->object()));
        }

        return api_response($response->json(), $response->status());
    }
}
