<?php

namespace App\Services;

use App\Data\DeliveryData;
use App\Enums\DiscountTypes;
use App\Enums\OrderStatuses;
use App\Models\Kit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Traits\OrderTrait;
use Illuminate\Support\Facades\DB;

class OrderService
{
    use OrderTrait;

    public function store($request): Order
    {
        DB::beginTransaction();

        $order = new Order();
        $order->payment_system_id = $request->payment_system_id;
        $order->user_id = $request->user_id;
        $order->user_name = $request->user_name;
        $order->user_phone = $request->user_phone;
        $order->user_email = $request->user_email;
        $order->paid_with_bonus = $request->bonus ?? 0;
        $total_cost = $this->calculateCart($request->products, $request->kits);
        $order->delivery_info = DeliveryData::fromStoreRequest($request, $total_cost);
        $order->comment = $request->comment;
        $order->city_id = $request->city;
        $order->save();
        if ($request->products) {
            foreach ($request->products as $cart_product) {
                /** @var Product $product */
                $product = Product::find($cart_product['id']);
                /** @var OrderItem $order_item */
                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->product_id = $cart_product['id'];
                $order_item->quantity = $cart_product['quantity'] ?? 1;
                $order_item->price = $product->promo_price ?? $product->price;
                $order_item->source = $product;
                $order_item->save();
            }
        }
        if ($request->kits) {
            foreach ($request->kits as $cart_kit) {
                /** @var Kit $kit */
                $kit = Kit::find($cart_kit['id']);
                foreach ($kit->items as $index => $kit_item) {
                    /** @var Product $product */
                    $product = Product::find($cart_kit['products'][$index]);
                    $price = $kit_item->discount_type == DiscountTypes::RUB ?
                        $product->price - $kit_item->discount_value :
                        floor($product->price - $product->price / 100 * $kit_item->discount_value);
                    $price = $price < 0 ? 0 : $price;
                    $order_item = new OrderItem();
                    $order_item->order_id = $order->id;
                    $order_item->product_id = $product->id;
                    $order_item->quantity = $cart_kit['quantity'];
                    $order_item->price = $price;
                    $order_item->source = $product;
                    $order_item->save();
                }
            }
        }
        DB::commit();

        return $order;
    }

    public function update(Order $order, $request)
    {
        $order->update($request->validated());
        if ($request->delivery) {
            $order->delivery_info = DeliveryData::fromUpdateRequest($request, $order);
            $order->update();
        }

        return $order;
    }

    public function cancel(Order $order): Order
    {
        /** @var OrderStatus $cancel_status */
        $cancel_status = OrderStatus::whereCode(OrderStatuses::CANCELED->value)->first();
        if ($cancel_status) {
            $order->prev_status_id = $order->status_id;
            $order->prev_status_code = $order->status_code;
            $order->status_id = $cancel_status->id;
            $order->status_code = $cancel_status->code;
            $order->update();
        }

        return $order->refresh();
    }

    public function updateOrderStatus(Order $order, OrderStatus $orderStatus): Order
    {
        if ($order->status_id != $orderStatus->id) {
            $order->prev_status_id = $order->status_id;
            $order->prev_status_code = $order->status_code;
            $order->status_id = $orderStatus->id;
            $order->status_code = $orderStatus->code;
            $order->update();
        }

        return $order->refresh();
    }
}
