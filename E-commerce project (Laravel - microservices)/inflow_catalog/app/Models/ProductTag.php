<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $product_id
 * @property $tag_id
 */
class ProductTag extends Pivot
{
    use UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $primaryKey = "id";
    protected $fillable = [
        'product_id',
        'tag_id'
    ];


}
