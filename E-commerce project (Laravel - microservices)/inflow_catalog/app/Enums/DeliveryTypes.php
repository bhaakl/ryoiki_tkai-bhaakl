<?php

namespace App\Enums;

enum DeliveryTypes: string
{
    case DELIVERY = 'delivery';
    case PICKUP = 'pickup';

    public function name(): string
    {
        return match($this) {
            DeliveryTypes::DELIVERY => 'Доставка',
            DeliveryTypes::PICKUP => 'Самовывоз',
        };
    }
}
