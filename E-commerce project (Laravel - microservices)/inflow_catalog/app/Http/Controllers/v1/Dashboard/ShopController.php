<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Dashboard\Shop\ShopResource;
use App\Services\Payment\PaymentService;

class ShopController extends Controller
{
    public function __construct(protected ?PaymentService $paymentService = null)
    {
    }

    public function index()
    {
        $data = (object) [
            'paidPays' => $this->paymentService->totalPaidAmount(),
            'totalOrders' => \App\Models\Order::count(),
            'totalItems' => \App\Models\Category::root()->count()
        ];

        return ShopResource::make($data);
    }

}