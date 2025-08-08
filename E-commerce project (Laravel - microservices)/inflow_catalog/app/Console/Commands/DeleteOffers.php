<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Console\Command;

class DeleteOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-offers';

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
                Product::whereNull('parent_id')->withTrashed()->whereNotNull('deleted_at')->chunk(10, function ($products) {
                    foreach ($products as $product) {
                        $product->offers()->delete();
                    }
                });
            }
        });
    }
}
