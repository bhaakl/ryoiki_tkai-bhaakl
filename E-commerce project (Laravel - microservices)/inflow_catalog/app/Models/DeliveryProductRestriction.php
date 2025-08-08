<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $delivery_id
 * @property $product_restriction_id
 * @property $all_day
 */
class DeliveryProductRestriction extends Pivot
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $primaryKey = "id";
    protected $fillable = [
        'delivery_id',
        'product_restriction_id',
        'all_day'
    ];

    public function intervals()
    {
        return $this->hasManyThrough(DeliveryInterval::class, DeliveryProductRestrictionInterval::class, 'dpr_id', 'id');
    }
}
