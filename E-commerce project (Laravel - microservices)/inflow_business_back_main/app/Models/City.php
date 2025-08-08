<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $ext_id
 * @property $name
 * @property $is_active
 */
class City extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'ext_id',
        'name',
        'is_active'
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
