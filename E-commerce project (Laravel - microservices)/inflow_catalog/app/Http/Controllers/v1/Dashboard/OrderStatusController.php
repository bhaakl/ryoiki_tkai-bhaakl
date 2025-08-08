<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Enums\OrderStatuses;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\OrderStatusCreateRequest;
use App\Http\Requests\v1\OrderStatusUpdateRequest;
use App\Http\Resources\v1\Dashboard\Order\ChiefOrderStatusCollection;
use App\Http\Resources\v1\Dashboard\Order\ChiefOrderStatusResource;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function getCodes()
    {
        return OrderStatuses::cases();
    }

    public function index()
    {
        $statuses = OrderStatus::orderBy('code')->paginate();

        return new ChiefOrderStatusCollection($statuses);
    }

    public function dropout()
    {
        $statuses = OrderStatus::orderBy('code')->get();

        return ChiefOrderStatusResource::collection($statuses);
    }

    public function store(OrderStatusCreateRequest $request)
    {
        $status = OrderStatus::create($request->validated());

        return new ChiefOrderStatusResource($status);
    }

    public function update(OrderStatusUpdateRequest $request, OrderStatus $order_status)
    {
        $order_status->update($request->validated());

        return new ChiefOrderStatusResource($order_status);
    }

    public function destroy(OrderStatus $order_status)
    {
        if (Order::where('status_id', $order_status->id)->orWhere('prev_status_id', $order_status->id)->exists()) {
            return response()->json(['message' => 'Нельзя удалить используемый статус'], 400);
        }
        if (!OrderStatus::whereCode($order_status->code)->where('id', '!=', $order_status->id)->exists()) {
            return response()->json(['message' => 'Нельзя удалить единственный статус'], 400);
        }
        $order_status->delete();

        return response()->json(['message' => 'Статус удалён']);
    }
}
