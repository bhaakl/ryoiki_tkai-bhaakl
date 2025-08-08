<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Console\Command;

class AddExtraImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-extra-images';

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
        Tenant::chunk(10, function ($tenants) {
            foreach ($tenants as $tenant) {
                $tenant->makeCurrent();
                Product::whereNull('parent_id')->get()->each(function (Product $product) {
                    sleep(1);
                    $product->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection('extra');
                    $product->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection('extra');
                });
            }
        });
    }
}
