<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Str;

class PostObserver
{
    /**
     * Прослушивание события сохранения поста.
     */
    public function saving(Post $post): void
    {
        $post->slug = Str::slug($post->title, '-');
    }
}
