<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\RabbitMQService;
use App\Services\TenantServiceCreator;
use Illuminate\Console\Command;

class CreateTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-tenants';

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
        $service = new RabbitMQService();

        foreach (Tenant::all() as $tenant) {
            $service->createTenant($tenant);
        }
    }
}
