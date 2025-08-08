<?php

use App\Broadcasting\PostChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Каналы
|--------------------------------------------------------------------------
| Здесь можно зарегистрировать все каналы передачи событий, которые поддерживает приложение.
|
*/

Broadcast::channel('App.Models.User.{id}', fn ($user, $id) => (int) $user->id === (int) $id);

Broadcast::channel('post.{post}', PostChannel::class);
