<?php

namespace App\Enums;

enum MenuKeyIcons
{
    case Truck;

    public function url(): string {
        return match($this) {
            MenuKeyIcons::Truck => config('app.url') . '/images/icons/menu/flaticon_com_truck.png',
            'default' => null
        };
    }
}
