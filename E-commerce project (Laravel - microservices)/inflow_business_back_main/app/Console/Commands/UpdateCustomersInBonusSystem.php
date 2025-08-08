<?php

namespace App\Console\Commands;

use App\Events\CustomerUpdatedEvent;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateCustomersInBonusSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-customers-in-bonus-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Tenant::chunk(2, function ($tenants) {
            foreach ($tenants as $tenant) {
                $tenant->makeCurrent();

                /** @var AppSetting $appSetting */
                $appSetting = AppSetting::first();
                if (!$appSetting->getBonusEnabled()) {
                    return;
                }

                $response = Http::withHeaders(['tenant-uuid' => $appSetting->loyalty_uuid])->get(config('loyalty.url') . "/v1/customers/ids");
                if ($response->failed()) {
                    continue;
                }
                $ids = $response->object();
                Customer::whereNotIn('id', $ids)
                    ->where(function ($query) {
                        $query->where('phone_verified_at', '!=', null)->orWhere('email_verified_at', '!=', null);
                    })
                    ->chunk(10, function ($customers) {
                    foreach ($customers as $customer) {
                        event(new CustomerUpdatedEvent($customer));
                    }
                });
            }
        });
    }
}
