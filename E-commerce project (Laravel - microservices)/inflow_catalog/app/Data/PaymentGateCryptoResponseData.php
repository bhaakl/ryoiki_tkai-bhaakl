<?php

namespace App\Data;

class PaymentGateCryptoResponseData
{
    public function __construct(
        public bool $success,
        public ?string $orderId = null,
        public ?string $transactionId = null,
        public ?string $amount = null,
        public ?string $currency = null,
        public ?string $message = null,
    )
    {
    }

    public static function success($orderId, $transactionId, $amount): self
    {
        return new self(
            success: true,
            orderId: $orderId,
            transactionId: $transactionId,
            amount: $amount,
            currency: 'RUB'
        );
    }

    public static function fail($message): self
    {
        return new self(
            success: false,
            message: $message
        );
    }
}
