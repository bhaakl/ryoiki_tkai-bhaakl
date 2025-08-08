<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
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
     * Определить, может ли пользователь сохранить медиафайл.
     */
    public function store(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Определить, может ли пользователь удалить медиафайл.
     */
    public function delete(User $user, Media $medium): bool
    {
        return $user->isAdmin();
    }
}
