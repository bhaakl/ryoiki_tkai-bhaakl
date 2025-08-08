<?php

namespace App\Http\Controllers;

use App\Enums\AuthTypes;
use App\Enums\LoyaltyTypes;

class MainController extends Controller
{
    public function __invoke()
    {
        return api_response([
            'auth_types' => AuthTypes::cases(),
            'loyalty_types' => LoyaltyTypes::cases(),
        ]);
    }
}
