<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $date
 * @property $product_id
 * @property $total
 */
class ProductRestriction extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'date',
        'product_id',
        'total'
    ];

    public function deliveries()
    {
        return $this->hasMany(DeliveryProductRestriction::class);
    }
}
