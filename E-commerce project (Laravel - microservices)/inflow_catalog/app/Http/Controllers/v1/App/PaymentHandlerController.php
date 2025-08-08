<?php

namespace App\Http\Controllers\v1\App;

use App\Contracts\PaymentGateContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\PayWithCryptogramRequest;
use App\Http\Resources\v1\App\Order\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Tenant;
use App\Services\Payment\PaymentService;
use App\States\Payment\Canceled;
use App\States\Payment\Failed;
use App\States\Payment\Paid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentHandlerController extends Controller
{
    public function webhook(Tenant $tenant, Request $request)
    {
        $tenant->makeCurrent();
        /** @var Payment $payment */
        $payment = Payment::whereRequestId($request->request_id)->first();
        $payment->update([
            'payment_id' => $request->TransactionId
        ]);
        $event = $request->Event;
        Log::debug('payment webhook event: ' . $event);
        if ($event == 'Payment') {
            $payment->state->transitionTo(Paid::class);
            if ($payment->order_id) {
                $payment->order->update(['paid' => true]);
            }
        } elseif ($event == 'Fail') {
            $payment->state->transitionTo(Failed::class);
        } elseif ($event == 'Cancel') {
            $payment->state->transitionTo(Canceled::class);
        }
    }

    public function success(Tenant $tenant, Request $request)
    {
        Log::debug('payment success', [
            'request' => $request->isJson() ? $request->json() : $request->input(),
            'tenant' => $tenant->toArray()
        ]);
    }

    public function fail(Tenant $tenant, Request $request)
    {
        Log::debug('payment fail', [
            'request' => $request->isJson() ? $request->json() : $request->input(),
            'tenant' => $tenant->toArray()
        ]);
    }

    public function receiptCallback(Tenant $tenant, Request $request)
    {
        Log::debug('payment receiptCallback', [
            'request' => $request->isJson() ? $request->json() : $request->input(),
            'tenant' => $tenant->toArray()
        ]);
    }
}
