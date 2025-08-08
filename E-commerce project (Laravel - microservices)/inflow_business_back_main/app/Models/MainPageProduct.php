<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $main_page_block_id
 * @property $product_id
 * @property $title
 * @property $is_active
 */
class MainPageProduct extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'main_page_block_id',
        'product_id',
        'title',
        'is_active',
    ];
}
