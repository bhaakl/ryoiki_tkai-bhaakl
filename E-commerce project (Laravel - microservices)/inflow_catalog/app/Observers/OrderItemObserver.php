<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    public function created(OrderItem $orderItem): void
    {
        $orderItem->order->update(['updated_at' => now()]);
    }

    public function updated(OrderItem $orderItem): void
    {
        $orderItem->order->update(['updated_at' => now()]);
    }

    public function deleted(OrderItem $orderItem): void
    {
        $orderItem->order->update(['updated_at' => now()]);
    }
}
