<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Прослушивание события создания пользователя.
     */
    public function creating(User $user): void
    {
        $user->registered_at = now();
    }
}
