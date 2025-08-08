<?php

namespace App\Data;

use App\States\Payment\PaymentState;

class PaymentGateStatusResponseData
{
    public function __construct(
        public bool $success,
        public ?PaymentState $state = null,
        public ?string $payment_id = null,
        public ?string $message = null,
    )
    {
    }

    public static function success($state, $payment_id): self
    {
        return new self(
            success: true,
            state: $state,
            payment_id: $payment_id,
        );
    }

    public static function fail($message = null): self
    {
        return new self(
            success: false,
            message: $message ?? 'Ошибка проверки статуса платежа'
        );
    }
}
