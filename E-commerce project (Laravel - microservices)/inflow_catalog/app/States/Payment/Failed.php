<?php

namespace App\States\Payment;

class Failed extends PaymentState
{
    public static $name = 'failed';

    public function isSuccessful(): bool
    {
        return false;
    }

    public function isFinal(): bool
    {
        return true;
    }
}
