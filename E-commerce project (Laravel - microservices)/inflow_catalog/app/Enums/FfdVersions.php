<?php

namespace App\Enums;

use App\Services\Payment\Payselection\Fiscalization1_05;
use App\Services\Payment\Payselection\Fiscalization1_2;

enum FfdVersions: string
{
    case FFD1_05 = '1.05';
    case FFD1_2 = '1.2';

    public function FiscalizationClass(): string {
        return match($this) {
            self::FFD1_05 => Fiscalization1_05::class,
            self::FFD1_2 => Fiscalization1_2::class,
        };
    }
}
