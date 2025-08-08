<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecreateCatalogJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('RecreateCatalogJob dispatching');
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
            exec('rm -R /srv/www/inflow_catalog/storage/app/public/images');
        } catch (\Exception $exception) {
            Log::error('db:wipe job', [
                'exception' => $exception->getMessage(),
            ]);
        }
        Artisan::call("migrate --path=database/migrations/landlord --database=landlord");
        Log::debug('RecreateCatalogJob done');
    }
}
