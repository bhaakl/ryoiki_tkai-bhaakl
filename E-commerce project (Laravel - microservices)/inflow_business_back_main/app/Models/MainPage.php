<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $slider_l
 * @property $slider_s
 * @property $loyalty
 * @property $my_bookings
 * @property $my_orders
 */
class MainPage extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'slider_l',
        'slider_s',
        'loyalty',
        'my_bookings',
        'my_orders',
    ];

    protected $casts = [
        'slider_l' => 'bool',
        'slider_s' => 'bool',
        'loyalty' => 'bool',
        'my_bookings' => 'bool',
        'my_orders' => 'bool',
    ];
}
