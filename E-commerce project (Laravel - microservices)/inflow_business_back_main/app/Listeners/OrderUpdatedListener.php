<?php

namespace App\Listeners;

use App\Data\OrderData;
use App\Enums\LoyaltyTypes;
use App\Events\OrderUpdatedEvent;
use App\Models\AppSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderUpdatedListener implements ShouldQueue
{
    public $connection = 'database';
    public $queue = 'listeners';

    /**
     * Handle the event.
     */
    public function handle(OrderUpdatedEvent $event): void
    {
        Log::debug('OrderUpdatedListener handle');
        /** @var AppSetting $settings */
        $settings = AppSetting::first();
        if (!$settings->loyalty_uuid || $settings->loyalty_type == LoyaltyTypes::NONE) {
            return;
        }
        $url = config('loyalty.url') . '/' . $event->version . '/orders/' . $event->order->id;
        $data = OrderData::fromResponse($event->customer, $event->order)->toArray();
        $response = Http::withHeader('tenant-uuid', $settings->loyalty_uuid)->post($url, $data);
        Log::debug('order updated event', [
            'success' => $response->successful()
        ]);
    }
}
