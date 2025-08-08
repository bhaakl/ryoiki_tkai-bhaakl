<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $delivery_id
 * @property $min_amount
 * @property $max_amount
 * @property $price
 */
class DeliveryPriceCondition extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'delivery_id',
        'min_amount',
        'max_amount',
        'price',
    ];
}
