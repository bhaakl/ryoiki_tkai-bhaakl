<?php

namespace App\Providers;

use App\Contracts\PaymentGateContract;
use App\Contracts\PaymentSystemContract;
use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Services\Payment\Payselection\PayselectionGateContract;
use App\Services\PaymentSystemService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateContract::class, PayselectionGateContract::class);
        $this->app->bind(PaymentSystemContract::class, PaymentSystemService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        $this->loadJsonTranslationsFrom(__DIR__ . '/../lang');
        Gate::policy(Order::class, OrderPolicy::class);
    }
}
