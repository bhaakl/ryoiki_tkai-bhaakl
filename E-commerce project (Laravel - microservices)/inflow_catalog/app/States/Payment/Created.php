<?php

namespace App\States\Payment;

class Created extends PaymentState
{
    public static $name = 'created';

    public function isSuccessful(): bool
    {
        return false;
    }

    public function isFinal(): bool
    {
        return false;
    }
}
