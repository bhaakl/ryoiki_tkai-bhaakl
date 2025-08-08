<?php

namespace App\States\Payment;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class PaymentState extends State
{
    abstract public function isSuccessful(): bool;

    abstract public function isFinal(): bool;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Created::class)
            ->allowTransition(Created::class, Paid::class)
            ->allowTransition(Created::class, Failed::class)
            ->allowTransition(Created::class, Canceled::class);
    }
}
