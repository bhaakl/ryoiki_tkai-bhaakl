<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Laravel\Scout\Searchable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $ext_id
 * @property $parent_id
 * @property $name
 * @property $active
 * @property $extra
 * @property $searchable
 */
class Category extends Model implements HasMedia
{
    use HasFactory, UsesTenantConnection, Searchable, InteractsWithMedia;

    protected $fillable = [
        'ext_id',
        'parent_id',
        'name',
        'active',
        'extra',
        'searchable',
    ];

    public function searchableAs(): string
    {
        return app('currentTenant')->database . '_categories_index';
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->where('active', true);
    }

    public function childrenRecursive() {
        return $this->children()->with('childrenRecursive');
    }

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

    public function scopeRoot($query)
    {
        return $query->where('parent_id', null);
    }

    public function scopeActive($query)
    {
        return $query->whereActive(true);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }
}
