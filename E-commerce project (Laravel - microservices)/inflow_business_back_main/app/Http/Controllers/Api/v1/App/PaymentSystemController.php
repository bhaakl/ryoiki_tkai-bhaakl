<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Resources\v1\App\PaymentSystemResource;
use App\Models\PaymentSystem;

class PaymentSystemController extends AppController
{
    public function index()
    {
        $paysystems = PaymentSystem::all();

        return api_response(PaymentSystemResource::collection($paysystems));
    }

    public function show(PaymentSystem $paymentSystem)
    {
        return api_response(new PaymentSystemResource($paymentSystem));
    }
}
