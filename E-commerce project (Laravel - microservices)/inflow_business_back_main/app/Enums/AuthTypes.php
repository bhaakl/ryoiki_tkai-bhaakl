<?php

namespace App\Enums;

enum AuthTypes: string
{
    case Email = "email";
    case Phone = "phone";
    case EmailAndPhone = "email_phone";
}
