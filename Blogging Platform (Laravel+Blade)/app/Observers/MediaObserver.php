<?php

namespace App\Observers;

use App\Models\Media;

class MediaObserver
{
    /**
     * Прослушивание события создания медиафайла.
     */
    public function creating(Media $medium): void
    {
        $medium->posted_at = now();
    }
}
