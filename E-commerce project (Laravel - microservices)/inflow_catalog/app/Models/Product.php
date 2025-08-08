<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Laravel\Scout\Searchable;

/**
 * @property  $id
 * @property  $article
 * @property  $barcode
 * @property  $ext_id
 * @property  $city_id
 * @property  $parent_id
 * @property  $title
 * @property  $description
 * @property  $price
 * @property  $promo_price
 * @property  $discount
 * @property  $bonus
 * @property  $sort
 * @property  $active
 * @property  $new
 * @property  $preorderable
 * @property  $popular
 * @property  $special
 * @property  $extra
 * @property  $bonus_multiplier
 * @property  $by_order
 * @property  $has_package
 * @property  $searchable
 */
class Product extends Model implements HasMedia
{
    use HasFactory, UsesTenantConnection, Searchable, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'id',
        'article',
        'barcode',
        'ext_id',
        'city_id',
        'parent_id',
        'title',
        'description',
        'price',
        'promo_price',
        'discount',
        'bonus',
        'sort',
        'active',
        'new',
        'preorderable',
        'popular',
        'special',
        'extra',
        'bonus_multiplier',
        'by_order',
        'searchable',
    ];

    public function searchableAs(): string
    {
        return app('currentTenant')->database . '_products_index';
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'article' => $this->article,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    /*public function shouldBeSearchable(): bool
    {
        return $this->parent_id != null;
    }*/

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function offers()
    {
        return $this->hasMany(Product::class, 'parent_id')->orderBy('sort')->active();
    }

    public function main_offer()
    {
        return $this->hasOne(Product::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id')->withTrashed();
    }

    public function similar()
    {
        return $this->belongsToMany(Product::class, ProductSimilar::class, 'product_id', 'similar_id');
    }

    public function property_enums()
    {
        return $this->belongsToMany(PropertyEnum::class);
    }

    public function property_strings()
    {
        return $this->hasMany(PropertyString::class);
    }

    public function getProperties()
    {
        $property_list = [];
        $properties = $this->property_strings;
        foreach ($properties as $property) {
            $prop = new \stdClass();
            $prop->id = $property->id;
            $prop->name = $property->property->name;
            $prop->type = 'string';
            $prop->value = $property->value;
            $property_list[] = $prop;
        }

        $property_enum_ids = ProductPropertyEnum::whereProductId($this->getAttribute('id'))->pluck('property_enum_id')->toArray();
        $property_enums = PropertyEnum::find($property_enum_ids);
        if (count($property_enums) > 0) {
            $properties = Property::active()->find($property_enums->pluck('property_id')->toArray());
            foreach ($properties as $property) {
                $prop = new \stdClass();
                $prop->id = $property->id;
                $prop->name = $property->name;
                $prop->type = 'enum';
                $prop->value_enum = PropertyEnum::wherePropertyId($property->id)->whereIn('id', $property_enum_ids)->pluck('value')->toArray();
                $property_list[] = $prop;
            }
        }

        return $property_list;
    }

    public function getPropertiesForDashboard()
    {
        $property_list = [];
        $properties = $this->property_strings;
        foreach ($properties as $property) {
            $prop = new \stdClass();
            $prop->id = $property->property_id;
            $prop->name = $property->property->name;
            $prop->type = 'string';
            $prop->value = $property->value;
            $property_list[] = $prop;
        }

        $property_enum_ids = ProductPropertyEnum::whereProductId($this->getAttribute('id'))->pluck('property_enum_id')->toArray();
        $property_enums = PropertyEnum::find($property_enum_ids);
        if (count($property_enums) > 0) {
            $properties = Property::find($property_enums->pluck('property_id')->toArray());
            foreach ($properties as $property) {
                $prop = new \stdClass();
                $prop->id = $property->id;
                $prop->name = $property->name;
                $prop->type = 'enum';
                $prop->value_enum = PropertyEnum::wherePropertyId($property->id)->whereIn('id', $property_enum_ids)->select('id', 'value')->get()->toArray();
                $property_list[] = $prop;
            }
        }

        return $property_list;
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->orderBy('value');
    }

    public function components()
    {
        return $this->belongsToMany(Component::class)->withPivot(['quantity', 'unit']);
    }

    public function getDescription()
    {
        if (!$this->parent_id) {
            return $this->description ?? $this->main_offer->description ?? '';
        } else {
            return $this->description ?? $this->parent->description ?? '';
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')->singleFile();
        $this->addMediaCollection('video')->singleFile();
        $this->addMediaCollection('extra');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, config('media-library.preview_width'), config('media-library.preview_height'))
            ->nonOptimized()
            ->nonQueued();
    }

    public function scopeActive($query)
    {
        return $query->whereActive(true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeIsOffer($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }
}
