<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\App\PaymentSystemResource;
use App\Models\PaymentSystem;
use App\Models\Tenant;
use App\Models\User;

class PaymentSystemController extends Controller
{
    protected Tenant $tenant;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
    }
    public function index()
    {
        $paysystems = PaymentSystem::all();

        return api_response(PaymentSystemResource::collection($paysystems));
    }
}
