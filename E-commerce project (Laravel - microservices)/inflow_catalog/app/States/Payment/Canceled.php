<?php

namespace App\States\Payment;

class Canceled extends PaymentState
{
    public static $name = 'canceled';

    public function isSuccessful(): bool
    {
        return false;
    }

    public function isFinal(): bool
    {
        return true;
    }
}
