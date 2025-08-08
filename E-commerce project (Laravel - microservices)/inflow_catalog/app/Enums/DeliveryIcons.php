<?php

namespace App\Enums;

enum DeliveryIcons: string
{
    case TRUCK = 'truck';
    case PACKAGE = 'package';

    public function icon(): string {
        return match($this) {
            DeliveryIcons::TRUCK => config('app.url') . '/images/icons/delivery/flaticon_com_truck.png',
            DeliveryIcons::PACKAGE => config('app.url') . '/images/icons/delivery/flaticon_com_package.png',
        };
    }
}
