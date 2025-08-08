<?php

namespace App\Http\Controllers\v1\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\App\Order\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Cache;

class OrderStatusController extends Controller
{
    public function __invoke()
    {
        $statuses = Cache::remember('order_status_list', 3600, function () {
            return OrderStatus::orderBy('code')->get();
        });

        return OrderStatusResource::collection($statuses);
    }
}
