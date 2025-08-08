<?php

namespace App\Services;

use App\Contracts\PaymentSystemContract;
use App\Data\PaymentSystemData;
use Illuminate\Support\Facades\Http;

class PaymentSystemService implements PaymentSystemContract
{

    public function getPaymentSystem(int $id): ?PaymentSystemData
    {
        $baseUrl = config('app.main_app_url');
        $response = Http::withHeaders([
            'tenant-uuid' => app('currentTenant')->uuid
        ])->get($baseUrl . '/api/app/v1/payment-systems/' . $id);

        if (!$response->successful() || !$response->object()->success) {
            return null;
        }

        return PaymentSystemData::fromData($response->object()->data);
    }
}
