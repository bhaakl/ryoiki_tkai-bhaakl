<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGateContract;
use App\Data\PaymentGateStatusResponseData;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(protected PaymentGateContract $paymentGate)
    {
    }

    public function payWithForm(Order $order)
    {
        $response = $this->paymentGate->payWithForm($order);

        Log::debug('PaymentService payWithForm', ['response' => $response]);
        if ($response->success) {
            Payment::create([
                'gate' => $this->paymentGate->getGateName(),
                'order_id' => $order->id,
                'payment_id' => $response->payment_id,
                'amount' => $response->amount,
                'request_id' => $response->request_id,
            ]);
        }

        return $response;
    }

    public function payWithCryptogram(Order $order, $cryptogram, $ip)
    {
        $response = $this->paymentGate->payWithCryptoGram($order, $cryptogram, $ip);

        if ($response->success) {
            Payment::create([
                'gate' => $this->paymentGate->getGateName(),
                'order_id' => $order->id,
                'payment_id' => $response->transactionId,
                'amount' => $response->amount,
            ]);
        }

        return $response;
    }

    public function getState(Payment $payment)
    {
        $response = $this->paymentGate->getState($payment);

        return $response;
    }

    public function totalPaidAmount($orderId = null)
    {
        return Payment::where('state', 'paid')->sum('amount');
    }
}
