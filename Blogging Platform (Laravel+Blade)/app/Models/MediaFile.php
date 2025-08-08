<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    /**
     * Таблица, связанная с моделью.
     *
     * @var string
     */
    protected $table = 'media_files';

    /**
     * Получение пользователя, которому принадлежит медиафайл.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
