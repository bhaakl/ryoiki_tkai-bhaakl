<?php

namespace App\Enums;

enum PaymentIcons: string
{
    case CONTACTLESS = 'contactless';
    case CARD = 'card';
    case CASH = 'cash';

    public function icon(): string {
        return match($this) {
            self::CONTACTLESS => config('app.url') . '/images/icons/payment/contactless.png',
            self::CARD => config('app.url') . '/images/icons/payment/card.png',
            self::CASH => config('app.url') . '/images/icons/payment/cash.png',
        };
    }
}
