<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Запуск служб приложения.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }

    /**
     * Регистрация всех служб приложения.
     */
    public function register(): void
    {
        Blade::directive('humanize_date', function (string $params) {
            return "<?php echo humanize_date($params); ?>";
        });
    }
}
