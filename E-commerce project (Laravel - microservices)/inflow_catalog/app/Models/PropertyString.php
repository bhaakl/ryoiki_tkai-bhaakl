<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $property_id
 * @property $product_id
 * @property $value
 */
class PropertyString extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'product_id',
        'value',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
