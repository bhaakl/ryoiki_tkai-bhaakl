<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeColors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-colors';

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
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            $tenant->makeCurrent();
            $setting = AppSetting::first();
            foreach ($setting->toArray() as $field => $value) {
                if (str_contains($value, '#00')) {
                    $setting->$field = str_replace('#00', '#FF', $value);
                    $setting->update();
                }
            }
        }
    }
}
