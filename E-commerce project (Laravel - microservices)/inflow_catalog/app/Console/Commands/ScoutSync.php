<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ScoutSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scout-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected array $modelsForSync = [
        \App\Models\Product::class,
        \App\Models\Category::class,
    ];

    protected string $searchableColumn = 'searchable';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Tenant::chunk(2, function($tenants) {
            /** @var Tenant $tenant */
            foreach ($tenants as $tenant) {
                $tenant->makeCurrent();
                Artisan::call('scout:sync-index-settings');
                foreach ($this->modelsForSync as $model) {
                    //$collection = $model::whereSearchable(false)->get();
                    $collection = $model::all();
                    if ($collection->isNotEmpty()) {
                        $collection->searchable();

                        $collection->each(function ($item) {
                            $item->update([$this->searchableColumn => true]);
                        });
                    }
                }
            }
        });
    }
}
