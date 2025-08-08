<?php

namespace App\Models;

use App\Enums\DeliveryIcons;
use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $city_id
 * @property $type
 * @property $icon
 * @property $name
 * @property $description
 * @property $base_cost
 * @property $has_intervals
 * @property $active
 * @property $mkad_min
 * @property $mkad_max
 */
class Delivery extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'city_id',
        'type',
        'icon',
        'name',
        'description',
        'base_cost',
        'has_intervals',
        'active',
        'mkad_min',
        'mkad_max',
    ];

    protected $casts = [
        'has_intervals' => 'boolean',
        'active' => 'boolean',
    ];

    public function intervals()
    {
        return $this->hasMany(DeliveryInterval::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class);
    }

    public function priceConditions()
    {
        return $this->hasMany(DeliveryPriceCondition::class);
    }

    public function restrictions()
    {
        return $this->hasMany(DeliveryRestriction::class);
    }

    public function getIntervals()
    {
        return $this->intervals()->count() > 0 ? $this->intervals : [];
    }

    public function getIcon()
    {
        return $this->getAttribute('icon');
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
