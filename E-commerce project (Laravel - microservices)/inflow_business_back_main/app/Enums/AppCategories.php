<?php

namespace App\Enums;

enum AppCategories: string
{
    case ESTORE = "online store";

    public static function toString($enumValue) {
        switch ($enumValue) {
            case self::ESTORE: return 'интернет-магазин';
        }
    }
}
