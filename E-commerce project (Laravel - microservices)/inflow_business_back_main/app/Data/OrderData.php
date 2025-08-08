<?php

namespace App\Data;

use App\Models\Customer;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class OrderData extends Data
{
    public function __construct(
        public int $customer_id,
        public ?string $customer_name,
        public ?string $customer_birthday,
        public Carbon $customer_created_at,
        public int $order_id,
        public int $order_amount,
        public int $order_bonus_amount,
        public Carbon $order_created_at,
        public bool $paid = false,
        public bool $canceled = false,
    )
    {

    }

    public static function fromResponse(Customer $customer, \stdClass $order): self
    {
        return new self(
            customer_id: $customer->id,
            customer_name: $customer->name,
            customer_birthday: $customer->birthday,
            customer_created_at: Carbon::parse($customer->created_at),
            order_id: $order->id,
            order_amount: $order->total,
            order_bonus_amount: $order->paid_with_bonus,
            order_created_at: Carbon::parse($order->created_at),
            paid: $order->paid,
            canceled: $order->canceled
        );
    }
}
