<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $kit_id
 * @property $product_id
 * @property $discount_type
 * @property $discount_value
 * @property $alt_category_id
 */
class KitItem extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'kit_id',
        'product_id',
        'discount_type',
        'discount_value',
        'alt_category_id',
    ];

    public function kit()
    {
        return $this->belongsTo(Kit::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
