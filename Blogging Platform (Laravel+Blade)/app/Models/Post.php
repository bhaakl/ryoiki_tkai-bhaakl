<?php

namespace App\Models;

use App\Scopes\PostedScope;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые могут быть присвоены массово.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'title',
        'content',
        'posted_at',
        'slug',
        'thumbnail_id',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $casts = [
        'posted_at' => 'datetime'
    ];

    /**
     * Метод "booting" модели.
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new PostedScope);
    }

    /**
     * Подготовка даты для сериализации массива / JSON.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Получение маршрутного ключа для модели.
     */
    public function getRouteKeyName(): string
    {
        if (request()->expectsJson()) {
            return 'id';
        }

        return 'slug';
    }

    /**
     * Определение области запроса для поиска постов.
     */
    public function scopeSearch(Builder $query, ?string $search)
    {
        if ($search) {
            return $query->where('title', 'LIKE', "%{$search}%");
        }
    }

    /**
     * Определение области запроса для упорядочивания постов по последним публикациям.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('posted_at', 'desc');
    }

    /**
     * Определение области запроса для включения только постов, опубликованных в прошлом месяце.
     */
    public function scopeLastMonth(Builder $query, int $limit = 5): Builder
    {
        return $query->whereBetween('posted_at', [carbon('1 month ago'), now()])
            ->latest()
            ->limit($limit);
    }

    /**
     * Определение области запроса для включения только постов, опубликованных на прошлой неделе.
     */
    public function scopeLastWeek(Builder $query): Builder
    {
        return $query->whereBetween('posted_at', [carbon('1 week ago'), now()])
            ->latest();
    }

    /**
     * Вернуть автора поста.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Вернуть миниатюру поста.
     */
    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    /**
     * Вернуть отрывок содержимого поста.
     */
    public function excerpt(int $length = 50): string
    {
        return Str::limit($this->content, $length);
    }

    /**
     * Вернуть true, если у поста есть миниатюра.
     */
    public function hasThumbnail(): bool
    {
        return filled($this->thumbnail_id);
    }
}