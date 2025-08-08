<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $payment_id
 * @property $entity
 * @property $entity_id
 * @property $type
 * @property $amount
 * @property $client_name
 * @property $comment
 */
class Transaction extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'payment_id',
        'entity',
        'entity_id',
        'type',
        'amount',
        'client_name',
        'comment',
    ];
}
