<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Events\OrderUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\OrderUpdateRequest;
use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ChiefOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected Tenant $tenant;

    public function __construct(protected ChiefOrderService $orderService)
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
    }

    public function index(Request $request)
    {
        $response = $this->orderService->index($request);

        return api_response($response->object(), $response->status());
    }

    public function show($id)
    {
        $response = $this->orderService->show($id);

        return api_response($response->object(), $response->status());
    }

    public function update($id, OrderUpdateRequest $request)
    {
        $response = $this->orderService->update($id, $request);
        if ($response->status() == 200) {
            $customer = Customer::find($response->object()->user_id);
            event(new OrderUpdatedEvent($customer, $response->object()));
        }

        return api_response($response->object(), $response->status());
    }

    public function updateStatus($id, Request $request)
    {
        $response = $this->orderService->updateStatus($id, $request);
        if ($response->status() == 200) {
            $customer = Customer::find($response->object()->user_id);
            event(new OrderUpdatedEvent($customer, $response->object()));
        }

        return api_response($response->object(), $response->status());
    }

    public function createItem($id, Request $request)
    {
        $response = $this->orderService->createItem($id, $request);
        if ($response->status() == 200) {
            $order = $this->orderService->show($id);
            if ($order->status() == 200) {
                $customer = Customer::find($order->object()->user_id);
                event(new OrderUpdatedEvent($customer, $order->object()));
            }
        }

        return api_response($response->object(), $response->status());
    }

    public function updateItem($id, $item, Request $request)
    {
        $response = $this->orderService->updateItem($id, $item, $request);
        if ($response->status() == 200) {
            $order = $this->orderService->show($id);
            if ($order->status() == 200) {
                $customer = Customer::find($order->object()->user_id);
                event(new OrderUpdatedEvent($customer, $order->object()));
            }
        }

        return api_response($response->object(), $response->status());
    }

    public function deleteItem($id, $item)
    {
        $response = $this->orderService->deleteItem($id, $item);
        if ($response->status() == 200) {
            $order = $this->orderService->show($id);
            if ($order->status() == 200) {
                $customer = Customer::find($order->object()->user_id);
                event(new OrderUpdatedEvent($customer, $order->object()));
            }
        }

        return api_response($response->object(), $response->status());
    }
}
