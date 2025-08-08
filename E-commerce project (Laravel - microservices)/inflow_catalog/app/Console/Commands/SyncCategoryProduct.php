<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Console\Command;

class SyncCategoryProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-category-product';

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
                Product::whereNull('parent_id')->chunk(10, function ($products) {
                    foreach ($products as $product) {
                        $categories = $product->categories()->pluck('categories.id')->toArray();
                        foreach ($product->offers as $offer) {
                            $offer->categories()->sync($categories);
                        }
                    }
                });
            }
        });
    }
}
