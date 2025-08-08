<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * URI, которые должны быть доступны, пока включен режим обслуживания.
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
