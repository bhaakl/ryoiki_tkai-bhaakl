<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Отображения прослушивателей событий для приложения.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Регистрация всех событий для приложения.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Определение необходимости автоматического обнаружения событий и слушателей.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
