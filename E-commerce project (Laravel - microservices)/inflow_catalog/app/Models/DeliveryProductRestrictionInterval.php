<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $dpr_id
 * @property $di_id
 */
class DeliveryProductRestrictionInterval extends Pivot
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $primaryKey = "id";
    protected $fillable = [
        'dpr_id',
        'di_id'
    ];
}
