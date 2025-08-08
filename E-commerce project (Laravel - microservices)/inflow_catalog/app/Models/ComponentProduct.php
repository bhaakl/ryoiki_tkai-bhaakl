<?php

namespace App\Models;

use App\Enums\MeasurementUnits;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $component_id
 * @property $product_id
 * @property $quantity
 * @property $unit
 */
class ComponentProduct extends Pivot
{
    use UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'component_id',
        'product_id',
        'quantity',
        'unit'
    ];

    protected $casts = [
        'unit' => MeasurementUnits::class
    ];
}
