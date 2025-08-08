<?php

namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class AppState extends State
{
    abstract public function name(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Configuration::class)
            ->allowTransition(Configuration::class, Testing::class)
            ->allowTransition(Testing::class, Configuration::class)
            ->allowTransition(Testing::class, Published::class);
    }
}
