<?php

namespace App\Data;

class PaymentGateResponseData
{
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public ?string $status = null,
        public ?string $payment_id = null,
        public ?string $payment_url = null,
        public ?string $request_id = null,
        public ?int $amount = null,
        public ?string $success_url = null,
        public ?string $fail_url = null,
    )
    {
    }

    public static function success($status = null,  $payment_url = null, $amount = null, $request_id = null, $payment_id = null, $success_url = null, $fail_url = null): self
    {
        return new self(
            success: true,
            status: $status,
            payment_id: $payment_id,
            payment_url: $payment_url,
            request_id: $request_id,
            amount: $amount,
            success_url: $success_url,
            fail_url: $fail_url
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
