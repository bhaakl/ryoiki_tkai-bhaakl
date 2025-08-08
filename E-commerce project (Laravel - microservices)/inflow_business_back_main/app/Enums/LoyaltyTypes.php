<?php

namespace App\Enums;

enum LoyaltyTypes: string
{
    case NONE = 'none';
    case BONUS = 'bonus';
    case DISCOUNT = 'discount';
}
