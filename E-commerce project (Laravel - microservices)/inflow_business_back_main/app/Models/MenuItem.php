<?php

namespace App\Models;

use App\Enums\MenuKeyIcons;
use App\Enums\MenuKeyTemplates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $value
 * @property $title
 * @property $position
 * @property $is_active
 * @property $content
 * @property $icon
 */
class MenuItem extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'value',
        'title',
        'position',
        'is_active',
        'content',
        'icon'
    ];

    protected $casts = [
        'value' => MenuKeyTemplates::class,
        'icon' => MenuKeyIcons::class,
        'is_active' => 'bool'
    ];

    public function getRequest(): ?int
    {
        if ($this->value->isCustom()) {
            return $this->id;
        }

        return null;
    }

    public function getTitle()
    {
        return $this->getAttribute('title') ?? $this->value->systemName();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
