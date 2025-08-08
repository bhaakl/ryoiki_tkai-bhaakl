<?php

namespace App\States\Payment;

class Paid extends PaymentState
{
    public static $name = 'paid';

    public function isSuccessful(): bool
    {
        return true;
    }

    public function isFinal(): bool
    {
        return true;
    }
}
