<?php

namespace App\Contracts;

use App\Data\PaymentSystemData;

interface PaymentSystemContract
{
    public function getPaymentSystem(int $id): ?PaymentSystemData;
}
