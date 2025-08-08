<?php

namespace App\Jobs;

use App\Enums\LoyaltyTypes;
use App\Events\CustomerUpdatedEvent;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\Tenant;
use App\Services\LoyaltyService;
use App\Traits\HelperTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateLoyaltyTypeJob implements ShouldQueue
{
    use Queueable, HelperTrait;

    /**
     * Create a new job instance.
     */
    public function __construct(protected AppSetting $appSetting)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $loyalty_service = new LoyaltyService();
        /** @var Tenant $tenant */
        $tenant = app('currentTenant');

        if ($this->appSetting->loyalty_type != LoyaltyTypes::NONE && !$this->appSetting->loyalty_uuid) {
            $response = $loyalty_service->register($tenant->uuid, $tenant->database, $this->appSetting->loyalty_type);
            if ($response->failed()) {
                Log::debug('loyalty register exception', [
                    'message' => $response->toException()->getMessage(),
                    'tenant' => $tenant->toArray()
                ]);
                $this->fail();
            }
            if ($response->successful()) {
                Log::debug('loyalty register success');
            }
            $this->appSetting->updateQuietly([
                'loyalty_uuid' => $response->object()->uuid
            ]);
        } elseif ($this->appSetting->loyalty_uuid) {
            $loyalty_service->updateLoyaltyType($this->appSetting->loyalty_uuid, $this->appSetting->loyalty_type);
        }
        Customer::where(function ($query) {
                $query->where('phone_verified_at', '!=', null)->orWhere('email_verified_at', '!=', null);
            })
            ->chunk(10, function ($customers) {
                foreach ($customers as $customer) {
                    event(new CustomerUpdatedEvent($customer));
                }
            });
    }
}
