<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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
     * Определить, может ли пользователь обновить пост.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    /**
     * Определить, может ли пользователь сохранить пост.
     */
    public function store(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Определить, может ли пользователь удалить пост.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
