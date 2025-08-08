<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $value
 */
class Tag extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $primaryKey = "id";
    protected $fillable = [
        'value'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
