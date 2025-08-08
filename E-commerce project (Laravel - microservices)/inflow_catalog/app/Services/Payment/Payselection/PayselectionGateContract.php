<?php

namespace App\Services\Payment\Payselection;

use App\Contracts\PaymentGateContract;
use App\Data\PaymentGateCryptoResponseData;
use App\Data\PaymentGateResponseData;
use App\Data\PaymentGateStatusResponseData;
use App\Enums\FfdVersions;
use App\Models\Acquiring;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayselectionGateContract implements PaymentGateContract
{
    public $statuses = [
        'new' => 'created',
        'pending' => 'created',
        'submitted' => 'created',
        'success' => 'paid',
        'declined' => 'failed',
        'voided' => 'canceled',
    ];

    public $keys = [
        'site_id',
        'secret',
        'public_key'
    ];

    protected Acquiring $acquiring;
    protected $base_url = 'https://gw.payselection.com';
    protected $site_id;
    protected $secret;
    protected $public_key;

    public function __construct()
    {
        Log::debug('construct ' . $this->getGateName());
        /** @var Acquiring $acquiring */
        $acquiring = Acquiring::whereName($this->getGateName())->first();
        if (!$acquiring) {
            return PaymentGateResponseData::fail("Данные эквайринга не найдены");
        }
        $this->acquiring = $acquiring;
        try {
            $this->site_id = decrypt($acquiring->keys->site_id);
        } catch (\Exception $exception) {
            $this->site_id = $acquiring->keys->site_id;
        }
        try {
            $this->secret = decrypt($acquiring->keys->secret);
        } catch (\Exception $exception) {
            $this->secret = $acquiring->keys->secret;
        }
        try {
            $this->public_key = decrypt($acquiring->keys->public_key);
        } catch (\Exception $exception) {
            $this->public_key = $acquiring->keys->public_key;
        }
    }

    public function payWithForm(Order $order): PaymentGateResponseData
    {
        $request_method = "POST";
        $url = "https://kucheryavo.ru";
        $x_site_id = $this->site_id;
        $x_request_id = Str::uuid()->toString();
        $success_url = config('app.url') . '/api/v1/payments/success/' . app('currentTenant')->id . '?gate=' . $this->getGateName();
        $fail_url = config('app.url') . '/api/v1/payments/fail/' . app('currentTenant')->id . '?gate=' . $this->getGateName();
        $params = [
            'PaymentRequest' => [
                'OrderId' => (string)$order->id,
                'Amount' => (string)$order->getTotalWithDelivery(),
                'Currency' => 'RUB',
                'Description' => 'Оплата товаров',
                'ExtraData' => [
                    'WebhookUrl' => config('app.url') . '/api/v1/payments/webhook/' . app('currentTenant')->id . '?gate=' . $this->getGateName() . '&request_id=' . $x_request_id,
                    'SuccessUrl' => $success_url,
                    'DeclineUrl' => $fail_url,
                ]
            ]
        ];
        if ($order->user_email) {
            $params['CustomerInfo'] = [
                'Email' => $order->user_email,
                'ReceiptEmail' => $order->user_email
            ];
        }
        if ($this->acquiring->ffd) {
            if ($this->acquiring->ffd == FfdVersions::FFD1_05) {
                $data = Fiscalization1_05::generate($order, $this->acquiring->ffd_keys->inn, $this->acquiring->ffd_keys->payment_address, $this->acquiring->ffd_keys->vat);
            } else {
                $data = Fiscalization1_2::generate($order, $this->acquiring->ffd_keys->inn, $this->acquiring->ffd_keys->payment_address, $this->acquiring->ffd_keys->vat, $this->acquiring->ffd_keys->sno);
            }
            $params['ReceiptData'] = $data;
        }

        $params_string = json_encode($params);
        $signature_string = "{$request_method}\n{$url}\n{$x_site_id}\n{$x_request_id}\n{$params_string}";

        $signature = hash_hmac("sha256", $signature_string, $this->public_key, false);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-SITE-ID' => $x_site_id,
            'X-REQUEST-ID' => $x_request_id,
            'X-REQUEST-SIGNATURE' => $signature,
        ])->post('https://webform.payselection.com/webpayments/paylink_create', $params);

        $response_object = $response->object();
        Log::debug('pay response', [
            'object' => $response_object,
            'status' => $response->status()
        ]);
        if ($response->status() != 201) {
            return PaymentGateResponseData::fail($response_object->Description ?? $response_object->Code);
        }

        return PaymentGateResponseData::success($this->statuses[Str::lower($response_object->Status)], $response_object->Url, $response_object->Amount, $x_request_id, $response_object->Id, $success_url, $fail_url);
    }

    public function payWithCryptogram(Order $order, string $cryptogram, $ip): PaymentGateCryptoResponseData
    {
        return PaymentGateCryptoResponseData::fail('Способ оплаты не поддерживается');
    }

    public function getState(Payment $payment): PaymentGateStatusResponseData
    {
        $request_method = "GET";
        $url = "https://kucheryavo.ru";
        $x_site_id = $this->site_id;
        $x_request_id = Str::uuid()->toString();
        $signature_string = "{$request_method}\n{$url}\n{$x_site_id}\n{$x_request_id}";
        $signature = hash_hmac("sha256", $signature_string, $this->secret);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-SITE-ID' => $x_site_id,
            'X-REQUEST-ID' => $x_request_id,
            'X-REQUEST-SIGNATURE' => $signature,
        ])->get($this->base_url . "/orders/" . $payment->order_id);

        if ($response->status() != 200) {
            Log::debug('getState response', [
                'url' => $this->base_url . "/orders/" . $payment->order_id,
                'error' => $response->body()
            ]);
            return PaymentGateStatusResponseData::fail('Ошибка');
        }

        $response_object = $response->object();

        return PaymentGateStatusResponseData::success($this->statuses[$response_object->TransactionState], $response_object->TransactionId);
    }

    public function getGateName(): string
    {
        return 'Payselection';
    }
}
