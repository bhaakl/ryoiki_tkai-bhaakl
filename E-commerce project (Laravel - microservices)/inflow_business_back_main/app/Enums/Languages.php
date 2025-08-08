<?php

namespace App\Enums;

enum Languages: string
{
    case RU = "russian";
    case EN = "english";

    public static function toString($enumValue) {
        switch ($enumValue) {
            case self::RU: return 'русский';
            case self::EN: return 'английский';
        }
    }
}
