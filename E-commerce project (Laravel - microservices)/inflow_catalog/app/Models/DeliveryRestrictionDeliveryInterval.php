<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $delivery_restriction_id
 * @property $delivery_interval_id
 */
class DeliveryRestrictionDeliveryInterval extends Pivot
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $primaryKey = "id";
    protected $table = 'delivery_restriction_interval';
    protected $fillable = [
        'delivery_restriction_id',
        'delivery_interval_id'
    ];
}
