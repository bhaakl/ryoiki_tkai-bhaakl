<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $ext_id
 * @property $property_id
 * @property $value
 */
class PropertyEnum extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'ext_id',
        'property_id',
        'value',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
