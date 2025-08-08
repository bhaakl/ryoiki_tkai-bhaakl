<?php

namespace App\Contracts;

use App\Data\PaymentGateCryptoResponseData;
use App\Data\PaymentGateResponseData;
use App\Data\PaymentGateStatusResponseData;
use App\Models\Order;
use App\Models\Payment;


interface PaymentGateContract
{
    public function payWithForm(Order $order): PaymentGateResponseData;

    public function payWithCryptoGram(Order $order, string $cryptogram, $ip): PaymentGateCryptoResponseData;

    public function getState(Payment $payment): PaymentGateStatusResponseData;

    public function getGateName(): string;
}
