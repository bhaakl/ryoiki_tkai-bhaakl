<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Arr::macro('pagination', function ($resource) {
            return [
                'current_page' => $resource->currentPage(),
                'per_page' => $resource->perPage(),
                'total' => $resource->total(),
                'total_pages' => $resource->lastPage(),
            ];
        });
    }
}
