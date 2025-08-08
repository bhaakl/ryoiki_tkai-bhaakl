<?php

namespace App\Models;

use App\Filters\QueryFilter;
use App\Observers\PropertyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $title
 * @property $name
 * @property $type
 * @property $cative
 */
#[ObservedBy([PropertyObserver::class])]
class Property extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'name',
        'type',
        'active',
    ];

    public function property_enums()
    {
        return $this->hasMany(PropertyEnum::class);
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
