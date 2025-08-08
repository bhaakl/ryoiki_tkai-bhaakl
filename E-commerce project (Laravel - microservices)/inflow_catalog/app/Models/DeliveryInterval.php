<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $delivery_id
 * @property $interval
 * @property $time_from
 * @property $time_to
 */
class DeliveryInterval extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'delivery_id',
        'interval',
        'time_from',
        'time_to'
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
