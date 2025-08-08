<?php

namespace App\Data;

use App\Http\Resources\v1\App\Store\StoreResource;
use App\Models\Delivery;
use App\Models\DeliveryInterval;
use App\Models\DeliveryPriceCondition;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Data;

class DeliveryData extends Data
{
    public function __construct(
        public ?int           $id,
        public ?string        $name,
        public ?string        $description,
        public ?string        $type,
        public ?string        $date,
        public ?int           $interval_id,
        public ?string        $interval,
        public ?string        $address,
        public ?string        $apartment,
        public ?string        $entrance,
        public ?string        $intercom,
        public ?string        $recipient_name,
        public ?string        $recipient_phone,
        public ?string        $recipient_email,
        public ?string        $recipient_note,
        public ?string        $courier_name,
        public ?string        $courier_phone,
        public ?bool          $anonymously,
        public ?StoreResource $store,
        public ?int           $cost = 0,
    )
    {

    }

    public static function fromStoreRequest($request, $order_total = 0): self
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::find($request->delivery['id']);

        $cost = $delivery->base_cost;
        if ($delivery->priceConditions()->count()) {
            /** @var DeliveryPriceCondition|null $condition */
            $condition = $delivery->priceConditions()
                ->where('min_amount', '<=', $order_total)
                ->where('max_amount', '>=', $order_total)
                ->first();
            if ($condition) {
                $cost = $condition->price;
            }
        }

        $interval = $delivery->has_intervals ? DeliveryInterval::find($request->delivery['interval']) : null;

        return new self(
            id: $delivery->id,
            name: $delivery->name,
            description: $delivery->description,
            type: $delivery->type,
            date: $request->delivery['date'] ?? null,
            interval_id: $interval ? $interval->id : null,
            interval: $interval ? $interval->interval : null,
            address: $request->delivery['address'] ?? null,
            apartment: $request->delivery['apartment'] ?? null,
            entrance: $request->delivery['entrance'] ?? null,
            intercom: $request->delivery['intercom'] ?? null,
            recipient_name: $request->delivery['recipient_name'] ?? null,
            recipient_phone: $request->delivery['recipient_phone'] ?? null,
            recipient_email: $request->delivery['recipient_email'] ?? null,
            recipient_note: $request->delivery['recipient_note'] ?? null,
            courier_name: $request->delivery['courier_name'] ?? null,
            courier_phone: $request->delivery['courier_phone'] ?? null,
            anonymously: $request->delivery['anonymously'] ?? false,
            store: isset($request->delivery['store']) ? new StoreResource(Store::find($request->delivery['store'])) : null,
            cost: $request->delivery['cost'] ?? $cost ?? 0
        );
    }

    public static function fromUpdateRequest($request, Order $order): self
    {
        $delivery_info = $order->delivery_info;
        $delivery = $request->delivery['id'] ? Delivery::find($request->delivery['id']) : null;


        if (array_key_exists('interval', $request->delivery)) {
            $interval = $request->delivery['interval'] != null ? DeliveryInterval::find($request->delivery['interval'])->interval : null;
            $interval_id = $interval ? $request->delivery['interval'] : null;
        } else {
            $interval = $delivery_info?->interval;
            $interval_id = $delivery_info?->interval_id ?? null;
        }
        Log::info('DeliveryData fromUpdateRequest', ['$request->delivery' => isset($request->delivery['store'])]);
        if (array_key_exists('store', $request->delivery)) {
            $store = $request->delivery['store'] != null ? new StoreResource(Store::find($request->delivery['store'])) : null;
            Log::info('fromUpdateRequest', ['store' => $store]);
        } else {
            $store = $delivery_info != null ? new StoreResource($delivery_info?->store) : null;
        }

        return new self(
            id: $delivery ? $delivery->id : $delivery_info?->id,
            name: $delivery ? $delivery->name : $delivery_info?->name,
            description: $delivery ? $delivery->description : $delivery_info?->description,
            type: $delivery ? $delivery->type : $delivery_info?->type,
            date: $delivery->date ?? null,
            interval_id: $interval_id,
            interval: $interval,
            address: $request->delivery['address'] ?? $delivery_info?->address,
            apartment: $request->delivery['apartment'] ?? $delivery_info?->apartment,
            entrance: $request->delivery['entrance'] ?? $delivery_info?->entrance,
            intercom: $request->delivery['intercom'] ?? $delivery_info?->intercom,
            recipient_name: $request->delivery['recipient_name'] ?? $delivery_info?->recipient_name,
            recipient_phone: $request->delivery['recipient_phone'] ?? $delivery_info?->recipient_phone,
            recipient_email: $request->delivery['recipient_email'] ?? $delivery_info?->recipient_email,
            recipient_note: $request->delivery['recipient_note'] ?? $delivery_info?->recipient_note,
            courier_name: $request->delivery['courier_name'] ?? $delivery_info?->courier_name,
            courier_phone: $request->delivery['courier_phone'] ?? $delivery_info?->courier_phone,
            anonymously: $request->delivery['anonymously'] ?? $delivery_info?->anonymously,
            store: $store,
            cost: isset($request->delivery['cost']) ? $request->delivery['cost'] : 0
        );
    }
}
