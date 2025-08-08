<?php

namespace App\Console\Commands;

use App\Enums\LoyaltyTypes;
use App\Models\AppSetting;
use App\Models\Tenant;
use Illuminate\Console\Command;

class DisableBonusSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonus:disable';

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
        Tenant::first()->makeCurrent();
        $setting = AppSetting::first();
        $setting->update([
            'loyalty_type' => LoyaltyTypes::NONE->value,
            'loyalty_uuid' => null
        ]);
    }
}
