<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Определить, является ли пользователь администратором для всех проверок авторизации.
     */
    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Определить, может ли пользователь обновить пользователя.
     */
    public function update(User $current_user, User $user): bool
    {
        return $current_user->id === $user->id;
    }
}
