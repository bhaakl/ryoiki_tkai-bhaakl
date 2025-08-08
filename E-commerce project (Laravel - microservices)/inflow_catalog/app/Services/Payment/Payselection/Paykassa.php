<?php

namespace App\Services\Payment\Payselection;

class Paykassa
{
    protected $base_url = 'https://receipt.pay-kassa.com';
    protected $merchant_id;
    protected $secretKey;

    public function createReceipt($operation_type)
    {
        $params = [
            'operation_type' => $operation_type,
            'callback_url' => config('app.url') . 'api/v1/payments/receipt/callback/' . app('currentTenant')->id,
            'receipt' => []
        ];
    }
}
