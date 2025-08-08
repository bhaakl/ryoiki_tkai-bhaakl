<?php

namespace App\Models;

use App\Enums\MeasurementUnits;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $name
 */
class Component extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
}
