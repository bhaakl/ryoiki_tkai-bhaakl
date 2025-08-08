<?php

namespace App\Console\Commands;

use App\Models\ConfirmationCode;
use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Console\Command;

class DeleteUnconfirmedCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-unconfirmed-customers';

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
        Tenant::chunk(2, function ($tenants) {
            foreach ($tenants as $tenant) {
                $tenant->makeCurrent();
                Customer::where('created_at', '<=', now()->subMinutes(15))
                    ->whereNull('email_verified_at')
                    ->whereNull('phone_verified_at')
                    ->delete();
            }
        });
    }
}
