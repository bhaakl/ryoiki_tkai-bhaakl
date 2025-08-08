<?php

namespace App\Providers;

use App\Models\Media;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Это пространство имен применяется к маршрутам контроллера.
     *
     * Кроме того, оно устанавливается в качестве корневого пространства имен генератора URL.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Путь к «домашнему» маршруту приложения.
     *
     * Этот параметр используется системой аутентификации Laravel для перенаправления пользователей после входа в систему.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Определение привязки модели маршрута, фильтров шаблонов и т. д.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Route::model('medium', Media::class);
    }

    /**
     * Определение маршрутов для приложения.
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapAuthRoutes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();
    }

    /**
     * Определение "web" маршрутов для приложения.
     *
     * Все эти маршруты получают состояние сессии, защиту от CSRF и т. д.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }

    /**
     * Определение маршрутов «api» для приложения.
     *
     * Эти маршруты, как правило, не имеют статуса.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }

    /**
     * Определение маршрутов «администратора» для приложения.
     *
     * Эти маршруты, как правило, не имеют статуса.
     */
    protected function mapAdminRoutes(): void
    {
        Route::prefix('admin')
            ->middleware(['web', 'auth', 'role:admin', 'verified'])
            ->as('admin.')
            ->group(base_path('routes/admin.php'));
    }

    /**
     * Определение маршрутов «auth» для приложения.
     *
     * Эти маршруты, как правило, не имеют статуса.
     */
    protected function mapAuthRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/auth.php'));
    }
}
