<?php

namespace App\Data;

use App\Models\Customer;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class CustomerData extends Data
{
    public function __construct(
        public int $customer_id,
        public ?string $customer_name,
        public ?string $customer_birthday,
        public Carbon $customer_created_at,
    )
    {

    }

    public static function fromModel(Customer $customer): self
    {
        return new self(
            customer_id: $customer->id,
            customer_name: $customer->name,
            customer_birthday: $customer->birthday,
            customer_created_at: Carbon::parse($customer->created_at),
        );
    }
}
