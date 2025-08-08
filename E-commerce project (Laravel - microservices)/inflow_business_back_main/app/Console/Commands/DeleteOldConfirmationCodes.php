<?php

namespace App\Console\Commands;

use App\Models\ConfirmationCode;
use App\Models\EmailChange;
use App\Models\PhoneChange;
use App\Models\Tenant;
use Illuminate\Console\Command;

class DeleteOldConfirmationCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-codes';

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
            ConfirmationCode::where('created_at', '<=', now()->subMinutes(15))->delete();
            EmailChange::where('created_at', '<=', now()->subMinutes(15))->delete();
            PhoneChange::where('created_at', '<=', now()->subMinutes(15))->delete();
        }
    }
}
