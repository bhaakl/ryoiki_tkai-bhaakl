<?php

namespace App\Http\Controllers\v1\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\PayWithCryptogramRequest;
use App\Http\Resources\v1\App\Order\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(protected ?PaymentService $paymentService = null)
    {
    }

    public function pay(Order $order, Request $request)
    {
        if ($request->header('user') != $order->user_id) {
            return response()->json(['message' => 'Доступ запрещён'], 403);
        }
        if ($order->paid) {
            return response()->json(['message' => 'Заказ уже оплачен']);
        }
        $payResponse = $this->paymentService->payWithForm($order, $request->ip());

        if (!$payResponse->success) {
            return response()->json(['message' => $payResponse->message], 400);
        }

        return response()->json($payResponse);
    }

    public function payWithCryptogram(Order $order, PayWithCryptogramRequest $request)
    {
        if ($request->header('user') != $order->user_id) {
            return response()->json(['message' => 'Доступ запрещён'], 403);
        }
        if ($order->paid) {
            return response()->json(['message' => 'Заказ уже оплачен']);
        }
        $payResponse = $this->paymentService->payWithCryptogram($order, $request->cryptogram, $request->ip());

        if (!$payResponse->success) {
            return response()->json(['message' => $payResponse->message], 400);
        }

        return response()->json($payResponse);
    }

    public function getState(Order $order, Request $request)
    {
        if ($request->header('user') != $order->user_id) {
            return response()->json(['message' => 'Доступ запрещён'], 403);
        }
        $payment = Payment::whereOrderId($order->id)->latest()->first();
        if (!$payment) {
            return response()->json('Платёж не найден', 400);
        }

        if ($payment->state->isSuccessful()) {
            if (!$order->paid) {
                $order->update(['paid' => true]);
            }

            return response()->json(new OrderResource($order->refresh()));
        }

        $stateResponse = $this->paymentService->getState($payment);

        if (!$stateResponse->success) {
            return response()->json($stateResponse->message, 400);
        }

        Log::debug('order state ' . $order->id, ['response' => $stateResponse]);

        if ($payment->state->canTransitionTo($stateResponse->state)) {
            $payment->state->transitionTo($stateResponse->state);
        }

        if ($payment->state->isSuccessful()) {
            if (!$order->paid) {
                $order->update(['paid' => true]);
            }
        }

        return response()->json(new OrderResource($order->refresh()));
    }
}
