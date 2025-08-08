<?php

namespace App\Listeners;

use App\Data\CustomerData;
use App\Enums\LoyaltyTypes;
use App\Events\CustomerUpdatedEvent;
use App\Models\AppSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerUpdatedListener implements ShouldQueue
{
    public $connection = 'database';
    public $queue = 'listeners';

    /**
     * Handle the event.
     */
    public function handle(CustomerUpdatedEvent $event): void
    {
        /** @var AppSetting $settings */
        $settings = AppSetting::first();
        if (!$settings->loyalty_uuid || $settings->loyalty_type == LoyaltyTypes::NONE) {
            return;
        }

        $url = config('loyalty.url') . '/' . $event->version . "/customers/update";
        $response = Http::withHeader('tenant-uuid', $settings->loyalty_uuid)->post($url, CustomerData::fromModel($event->customer)->toArray());
        if ($response->failed()) {
            Log::warning('CustomerUpdatedListener failed', ['response' => $response->body()]);
        }
        Log::debug('customer updated event', [
            'success' => $response->successful()
        ]);
    }
}
