<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $title
 * @property $category_id
 * @property $is_active
 */
class Banner extends Model implements HasMedia
{
    use UsesTenantConnection, InteractsWithMedia;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'title',
        'category_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, config('media-library.preview_width'), config('media-library.preview_height'))
            ->nonOptimized()
            ->nonQueued();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
