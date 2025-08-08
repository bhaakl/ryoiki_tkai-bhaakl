<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $city_id
 * @property $name
 * @property $address
 * @property $phone
 * @property $subway
 * @property $lon
 * @property $lat
 * @property $open
 * @property $active
 * @property $pickup
 * @property $shop
 */
class Store extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'city_id',
        'name',
        'address',
        'phone',
        'subway',
        'lon',
        'lat',
        'open',
        'active',
        'pickup',
        'shop',
    ];

    protected $casts = [
        'active' => 'boolean',
        'pickup' => 'boolean',
        'shop' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->whereActive(true);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }
}
