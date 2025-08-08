<?php

namespace App\Enums;

enum Roles: string
{
    case SUPER_ADMIN = "super_admin";
    case CHIEF = "chief";
    case CUSTOMER = "customer";
}
