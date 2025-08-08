<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateOrderTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-order-total';

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
                Order::whereNull('total')->chunk(100, function ($orders) use ($tenant) {
                    foreach ($orders as $order) {
                        $order->update(['updated_at' => now()]);
                    }
                });
            }
        });
    }
}
