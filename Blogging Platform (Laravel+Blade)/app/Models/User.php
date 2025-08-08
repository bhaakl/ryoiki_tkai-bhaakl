<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\MediaFile;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Атрибуты, которые можно массово назначить.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'provider', 'provider_id', 'registered_at', 'profile_img_id'
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $casts = [
        'registered_at' => 'datetime'
    ];

    public function profileImage()
    {
        return $this->belongsTo(MediaFile::class, 'profile_img_id');
    }

    /**
     * Атрибуты, которые должны быть скрыты в массивах.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Получение полного имени пользователя в виде заголовка.
     */
    public function getFullnameAttribute(): string
    {
        return Str::title($this->name);
    }

    /**
     * Определение области запроса для включения только пользователей, зарегистрированных на прошлой неделе.
     */
    public function scopeLastWeek(Builder $query): Builder
    {
        return $query->whereBetween('registered_at', [carbon('1 week ago'), now()])
            ->latest();
    }

    /**
     * Определение области запроса для упорядочивания пользователей по последней регистрации.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('registered_at', 'desc');
    }

    /**
     * Определение области запроса для фильтрации доступных авторов пользователей.
     */
    public function scopeAuthors(Builder $query): Builder
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('roles.name', Role::ROLE_ADMIN)
                ->orWhere('roles.name', Role::ROLE_USER);
        });
    }

    /**
     * Проверка, может ли пользователь быть автором.
     */
    public function canBeAuthor(): bool
    {
        return $this->isAdmin() || $this->isEditor();
    }

    /**
     * Проверка того, есть ли у пользователя роль
     */
    public function hasRole(string $role): bool
    {
        return $this->roles->where('name', $role)->isNotEmpty();
    }

    /**
     * Проверка того, имеет ли пользователь роль admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ROLE_ADMIN);
    }

    /**
     * Проверка того, имеет ли пользователь роль user
     */
    public function isEditor(): bool
    {
        return $this->hasRole(Role::ROLE_USER);
    }

    /**
     * Вернуть посты пользователя.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * Вернуть роли пользователя
     */
    public function roles(): belongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
    
}
