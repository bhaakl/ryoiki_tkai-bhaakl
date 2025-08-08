<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Observers\AppSettingObserver;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        AppSetting::observe(AppSettingObserver::class);
    }
}
