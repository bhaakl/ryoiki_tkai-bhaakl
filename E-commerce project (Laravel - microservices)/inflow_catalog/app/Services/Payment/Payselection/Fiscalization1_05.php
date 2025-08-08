<?php

namespace App\Services\Payment\Payselection;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class Fiscalization1_05
{
    public const VERSION = '1.05';

    public $required_fields = [
        'inn' => [
            'required',
            'digits_between:10,12',
            'regex:/^(\d{10}|\d{12})$/'
        ],
        'payment_address' => [
            'required',
            'url'
        ],
        'vat' => [
            'required',
            'in:none,vat0,vat10,vat110,vat20,vat120',
        ]
    ];

    public static function generate(Order $order, $inn, $payment_address, $vat)
    {
        $client = [];
        if ($order->user_name) {
            $client['name'] = $order->user_name;
        }
        if ($order->user_email) {
            $client['email'] = $order->user_email;
        }
        if ($order->user_phone) {
            $client['phone'] = Str::contains($order->user_phone, "+") ? $order->user_phone : "+" . $order->user_phone;
        }

        return [
            'timestamp' => now(),
            'receipt' => [
                'client' => $client,
                'company' => [
                    'inn' => $inn,
                    'payment_address' => $payment_address
                ],
                'items' => self::mapItems($order),
                'payments' => [
                    [
                        'type' => 1,
                        'sum' => $order->getTotalWithDelivery()
                    ]
                ],
                'vats' => [
                    [
                        'type' => $vat,
                        'sum' => self::calculateVat($order, $vat)
                    ]
                ],
                'total' => $order->getTotalWithDelivery()
            ]
        ];
    }

    protected static function mapItems(Order $order): array
    {
        $items = [];

        /** @var OrderItem $order_item */
        foreach ($order->items as $order_item) {
            $items[] = [
                'name' => $order_item->source->title,
                'price' => $order_item->price,
                'quantity' => $order_item->quantity,
                'sum' => $order_item->price * $order_item->quantity,
                'payment_method' => 'full_prepayment',
                'payment_object' => 'commodity',
            ];
        }

        return $items;
    }

    protected static function calculateVat(Order $order, $vat): float
    {
        switch ($vat) {
            case "vat10":
                $sum = (float)number_format($order->getTotalWithDelivery() / 100 * 10, 2, '.', '');
                break;
            case "vat110":
                $sum = (float)number_format($order->getTotalWithDelivery() / 110 * 10, 2, '.', '');
                break;
            case "vat20":
                $sum = (float)number_format($order->getTotalWithDelivery() / 100 * 20, 2, '.', '');
                break;
            case "vat120":
                $sum = (float)number_format($order->getTotalWithDelivery() / 120 * 20, 2, '.', '');
                break;
            default:
                $sum = 0;
                break;
        }

        return $sum;
    }
}
