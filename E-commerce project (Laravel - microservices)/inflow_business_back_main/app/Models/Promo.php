<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $main_page_block_id
 * @property $title
 * @property $description
 * @property $category_id
 * @property $is_active
 */
class Promo extends Model implements HasMedia
{
    use UsesTenantConnection, InteractsWithMedia;

    protected $fillable = [
        'id',
        'main_page_block_id',
        'title',
        'description',
        'category_id',
        'is_active',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')->singleFile();
        $this->addMediaCollection('slides');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
