<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Console\Command;

class DeliveryInfoFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delivery-info-fix';

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
                Order::whereDeliveryInfo('{}')->chunk(100, function ($orders) use ($tenant) {
                    foreach ($orders as $order) {
                        $order->update(['delivery_info' => null]);
                    }
                });
            }
        });
    }
}
