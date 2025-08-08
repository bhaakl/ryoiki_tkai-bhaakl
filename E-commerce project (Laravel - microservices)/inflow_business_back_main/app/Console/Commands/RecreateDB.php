<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\RabbitMQService;
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
            try {
                $tenants = Tenant::pluck('database')->toArray();
                foreach ($tenants as $tenant) {
                    $query = "DROP DATABASE IF EXISTS $tenant";
                    DB::statement($query);
                }
            } catch (\Exception $exception) {
            }

            Artisan::call("db:wipe --database=landlord --force");
            $output = Artisan::output();
            echo $output;
        } catch (\Exception $exception) {
            Log::error('recreate-database', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
        }

        $rabbit = new RabbitMQService();
        $rabbit->recreateDB();

        exec('rm -R /srv/www/inflow_business/storage/app/public/media');
        Artisan::call("migrate --path=database/migrations/landlord --database=landlord --seed --force");
        $output = Artisan::output();
        echo $output;
    }
}
