<?php

namespace App\Console\Commands;

use App\Models\NavBarItem;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedNavBar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-nav-bar';

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
        foreach (Tenant::all() as $tenant) {
            $tenant->makeCurrent();
            NavBarItem::truncate();
            Artisan::call('db:seed', ['class' => 'NavBarItemSeeder']);
        }
    }
}
