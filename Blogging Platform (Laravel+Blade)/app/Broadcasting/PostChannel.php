<?php

namespace App\Broadcasting;

use App\Models\Post;
use App\Models\User;

class PostChannel
{
    /**
     * Проверка подлинности доступа пользователя к этому каналу.
     */
    public function join(User $user, Post $post): bool
    {
        return true;
    }
}
