<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class PostedScope implements Scope
{
    /**
     * Применить область видимости к заданному конструктору запросов Eloquent.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user() ?? Auth::guard('sanctum')->user();

        // если не подключен или подключен, но не является администратором
        if (!$user || !$user->isAdmin()) {
            $builder->where('posted_at', '<=', now());
        }
    }
}
