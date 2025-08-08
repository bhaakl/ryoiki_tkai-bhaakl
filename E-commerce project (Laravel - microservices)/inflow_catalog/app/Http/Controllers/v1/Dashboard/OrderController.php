<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Enums\OrderStatuses;
use App\Filters\OrderFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\OrderItemCreateRequest;
use App\Http\Requests\v1\OrderItemUpdateRequest;
use App\Http\Requests\v1\OrderUpdateRequest;
use App\Http\Requests\v1\OrderUpdateStatusRequest;
use App\Http\Resources\v1\Dashboard\Order\OrderCollection;
use App\Http\Resources\v1\Dashboard\Order\OrderDetailResource;
use App\Http\Resources\v1\Dashboard\Order\OrderItemResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(Request $request, OrderFilter $filter)
    {
        $orders = Order::filter($filter)->latest('id')->paginate($request->per_page ?? self::PER_PAGE);

        return new OrderCollection($orders);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return new OrderDetailResource($order);
    }

    public function update($id, OrderUpdateRequest $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $this->orderService->update($order, $request);

        return new OrderDetailResource($order);
    }

    public function updateStatus($id, OrderUpdateStatusRequest $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        /** @var OrderStatus $order_status */
        $order_status = OrderStatus::findOrFail($request->status_id);

        $order = $this->orderService->updateOrderStatus($order, $order_status);

        return new OrderDetailResource($order);
    }

    public function updateItem($id, $item, OrderItemUpdateRequest $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        /** @var OrderItem $order_item */
        $order_item = OrderItem::findOrFail($item);

        if ($request->quantity) {
            $order_item->quantity = $request->quantity;
        }
        if ($request->price) {
            $source = $order_item->source;
            $source->price = $request->price;
            $order_item->source = $source;
            $order_item->price = $source->promo_price ?? $source->price;
        }
        if (isset($request->promo_price)) {
            $source = $order_item->source;
            $source->promo_price = $request->promo_price;
            $order_item->source = $source;
            $order_item->price = $source->promo_price ?? $source->price;
        }
        $order_item->update();

        return new OrderItemResource($order_item->refresh());
    }

    public function createItem($id, OrderItemCreateRequest $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        /** @var Product $product */
        $product = Product::find($request->product_id);
        if (!$product->parent_id) {
            $product = $product->main_offer;
        }

        $order_item = new OrderItem();
        $order_item->order_id = $order->id;
        $order_item->product_id = $product->id;
        $order_item->price = $product->promo_price ?? $product->price;
        $order_item->source = $product;
        $order_item->save();

        return new OrderItemResource($order_item->refresh());
    }

    public function deleteItem($id, $item)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        /** @var OrderItem $order_item */
        $order_item = OrderItem::findOrFail($item);

        if ($order->paid) {
            return response()->json(['error' => 'Заказ оплачен'], 400);
        }

        $status = OrderStatuses::from($order->status_code);
        if ($status->isFinal()) {
            return response()->json(['error' => 'Заказ завершён'], 400);
        }

        $order_item->delete();

        return response()->json(['message' => 'ok']);
    }
}
