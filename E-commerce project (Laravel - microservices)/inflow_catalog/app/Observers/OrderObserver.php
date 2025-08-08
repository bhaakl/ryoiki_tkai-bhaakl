<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function creating(Order $order): void
    {
        $order->total = $order->getTotal();
        $order->total_with_delivery = $order->getTotalWithDelivery();
    }

    public function updating(Order $order): void
    {
        $order->total = $order->getTotal();
        $order->total_with_delivery = $order->getTotalWithDelivery();
    }
}
