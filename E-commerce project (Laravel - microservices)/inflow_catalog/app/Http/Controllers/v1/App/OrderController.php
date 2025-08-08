<?php

namespace App\Http\Controllers\v1\App;

use App\Enums\OrderStatuses;
use App\Filters\OrderFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\OrderOneClickStoreRequest;
use App\Http\Requests\v1\OrderStoreRequest;
use App\Http\Resources\v1\App\Order\OrderCollection;
use App\Http\Resources\v1\App\Order\OrderResource;
use App\Http\Resources\v1\App\Order\OrderStatusResource;
use App\Models\Delivery;
use App\Models\DeliveryInterval;
use App\Models\DeliveryRestriction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Services\OrderService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(Request $request, OrderFilter $filter)
    {
        $orders = Order::whereUserId($request->header('user'))
            ->filter($filter)
            ->latest()
            ->where('delivery_info', '!=', "{}")
            ->paginate($request->per_page ?? self::PER_PAGE);

        return new OrderCollection($orders);
    }

    public function latest(Request $request)
    {
        $orders = Order::whereUserId($request->header('user'))
            ->latest()
            ->limit(5)
            ->get();

        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        if (\request()->header('user') != $order->user_id) {
            throw new AccessDeniedHttpException('Доступ запрещён');
        }

        return new OrderResource($order);
    }

    public function cancel(Order $order)
    {
        if (\request()->header('user') != $order->user_id) {
            throw new AccessDeniedHttpException('Доступ запрещён');
        }
        if ($order->paid) {
            return response()->json('Нельзя отменить оплаченный заказ', 400);
        }
        if ($order->status_code == OrderStatuses::DONE->value) {
            return response()->json('Нельзя отменить, заказ в обработке', 400);
        }

        $order = $this->orderService->cancel($order);

        return new OrderResource($order);
    }

    public function store(OrderStoreRequest $request)
    {
        if (isset($request->delivery['date'])) {
            $date = CarbonImmutable::parse($request->delivery['date']);
            /** @var Delivery $delivery */
            $delivery = Delivery::find($request->delivery['id']);
            $restrictions = $delivery->restrictions()->whereDate('delivery_restrictions.date_from', '<=', $date)->whereDate('delivery_restrictions.date_to', '>=', $date)->get();
            if ($restrictions->count() > 0 && !$delivery->has_intervals) {
                return response()->json('Доставка в указанную дату невозможна', 400);
            } elseif ($restrictions->count() > 0 && $delivery->has_intervals && $request->delivery['interval']) {
                $interval = DeliveryInterval::find($request->delivery['interval']);
                /** @var DeliveryRestriction $restriction */
                foreach ($restrictions as $restriction) {
                    if ($restriction->intervals->contains($interval)) {
                        return response()->json('Доставка в указанное время невозможна', 400);
                    }
                }
            }
        }
        $order = $this->orderService->store($request);

        return new OrderResource($order->refresh());
    }

    public function oneClick(OrderOneClickStoreRequest $request)
    {
        DB::beginTransaction();
        $order = new Order();
        $order->user_id = $request->header('user');
        $order->user_name = $request->user_name;
        $order->user_phone = $request->user_phone;
        $order->user_email = $request->user_email;
        $order->quick = true;
        $order->save();

        /** @var Product $product */
        $product = Product::find($request->product);

        $order_item = new OrderItem();
        $order_item->order_id = $order->id;
        $order_item->product_id = $product->id;
        $order_item->price = $product->promo_price ?? $product->price;
        $order_item->source = $product;
        $order_item->save();

        DB::commit();

        return new OrderResource($order->refresh());
    }

    public function orderStatusList()
    {
        $statuses = OrderStatus::all();

        return OrderStatusResource::collection($statuses);
    }
}
