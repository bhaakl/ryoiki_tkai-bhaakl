<?php

namespace App\Enums;

use App\Services\Payment\Payselection\PayselectionGateContract;

enum PaymentGates: string
{
    case Payselection = "Payselection";

    public function gate(): string {
        return match($this) {
            self::Payselection => PayselectionGateContract::class,
        };
    }
}
