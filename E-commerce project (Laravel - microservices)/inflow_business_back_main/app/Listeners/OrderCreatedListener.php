<?php

namespace App\Listeners;

use App\Data\OrderData;
use App\Enums\LoyaltyTypes;
use App\Events\OrderCreatedEvent;
use App\Models\AppSetting;
use App\Services\RabbitMQService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderCreatedListener implements ShouldQueue
{
    public $connection = 'database';
    public $queue = 'listeners';

    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEvent $event): void
    {
        /** @var AppSetting $settings */
        $settings = AppSetting::first();
        if (!$settings->loyalty_uuid || $settings->loyalty_type == LoyaltyTypes::NONE) {
            return;
        }
        $data = OrderData::fromResponse($event->customer, $event->order);
        $rabbit = new RabbitMQService();
        $rabbit->orderCreated(app('currentTenant'), $data);
    }
}
