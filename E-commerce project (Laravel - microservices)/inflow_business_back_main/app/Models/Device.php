<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $customer_id
 * @property $type
 * @property $token
 */
class Device extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'id',
        'customer_id',
        'type',
        'token',
    ];
}
