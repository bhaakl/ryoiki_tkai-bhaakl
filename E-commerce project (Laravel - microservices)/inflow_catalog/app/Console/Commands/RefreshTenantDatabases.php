<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-tenant-databases {tenant?}';

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
        if ($this->argument('tenant') != null) {
            Tenant::find($this->argument('tenant'))->makeCurrent();
            Artisan::call("migrate:refresh --database=tenant --seed");
        } else {
            Tenant::chunk(1, function ($tenants) {
                foreach ($tenants as $tenant) {
                    $tenant->makeCurrent();
                    Artisan::call("migrate:refresh --database=tenant --seed");
                }
            });
        }
    }
}
