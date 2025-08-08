<?php

use App\Console\Commands\DeleteOldConfirmationCodes;
use App\Console\Commands\DeleteUnconfirmedCustomers;
use App\Console\Commands\UpdateCustomersInBonusSystem;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command(DeleteOldConfirmationCodes::class)->everyMinute();
Schedule::command(DeleteUnconfirmedCustomers::class)->everyMinute();
Schedule::command(UpdateCustomersInBonusSystem::class)->everyMinute();
