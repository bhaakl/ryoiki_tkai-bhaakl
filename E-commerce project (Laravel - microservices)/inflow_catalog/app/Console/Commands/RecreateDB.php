<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecreateDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:recreate';

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
        try {
            $tenants = Tenant::pluck('database')->toArray();
            foreach ($tenants as $tenant) {
                $query = "DROP DATABASE IF EXISTS $tenant";
                DB::statement($query);
            }
        } catch (\Exception $exception) {
            Log::error('recreate-catalog job', [
                'exception' => $exception->getMessage(),
            ]);
        }
        try {
            Log::info('db:wipe job');
            Artisan::call("db:wipe --database=landlord --force");
        } catch (\Exception $exception) {
            Log::error('db:wipe job', [
                'exception' => $exception->getMessage(),
            ]);
        }
        Artisan::call("migrate --path=database/migrations/landlord --database=landlord");

        Tenant::create([
            'id' => 1,
            'uuid' => '0eabf35d-a2a3-4c9a-b1a8-9691373902f3',
            'database' => 'tenant_1',
        ]);
    }
}
