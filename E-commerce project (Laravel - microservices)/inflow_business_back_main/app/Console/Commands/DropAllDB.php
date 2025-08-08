<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DropAllDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop';

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
        $tenants = Tenant::pluck('database')->toArray();
        foreach ($tenants as $tenant) {
            $query = "DROP DATABASE IF EXISTS $tenant";
            DB::statement($query);
        }
        $query = "DROP DATABASE IF EXISTS " . config('database.connections.mysql.database');
        DB::statement($query);
    }
}
