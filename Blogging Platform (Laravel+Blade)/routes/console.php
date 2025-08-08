<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Маршруты
|--------------------------------------------------------------------------
|
| В этом файле можно определить все консольные команды, основанные на Closure
| команд. Каждый Closure привязывается к экземпляру команды, что позволяет
| простой подход к взаимодействию с методами ввода-вывода каждой команды.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
