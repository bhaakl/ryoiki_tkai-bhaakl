<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $ext_id
 * @property $uid
 * @property $name
 */
class Kit extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'ext_id',
        'uid',
        'name'
    ];

    public function items()
    {
        return $this->hasMany(KitItem::class);
    }
}
