<?php

namespace App\Models;

use App\States\Payment\PaymentState;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $gate
 * @property $order_id
 * @property $service_id
 * @property $state
 * @property $payment_id
 * @property $request_id
 * @property $amount
 */
class Payment extends Model
{
    use UsesTenantConnection, HasStates;

    protected $fillable = [
        'id',
        'gate',
        'order_id',
        'service_id',
        'state',
        'payment_id',
        'request_id',
        'amount',
    ];

    protected $casts = [
        'state' => PaymentState::class
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
