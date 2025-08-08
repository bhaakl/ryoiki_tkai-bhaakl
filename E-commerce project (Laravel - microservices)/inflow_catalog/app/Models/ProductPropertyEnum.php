<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $ext_id
 * @property $property_enum_id
 * @property $product_id
 */
class ProductPropertyEnum extends Pivot
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $primaryKey = "id";
    protected $fillable = [
        'ext_id',
        'property_enum_id',
        'product_id'
    ];

    public function property_enum()
    {
        return $this->belongsTo(PropertyEnum::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
