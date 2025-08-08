<?php

namespace App\Models;

use App\Enums\OrderStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $ext_id
 * @property $code
 * @property $name
 * @property $active
 */
class OrderStatus extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'ext_id',
        'code',
        'name',
        'active'
    ];

    public static function getInitialStatus()
    {
        $model = self::whereCode(OrderStatuses::CREATED)->first();
        if (!$model) {
            $model = OrderStatus::create([
                'code' => OrderStatuses::CREATED->value,
                'name' => OrderStatuses::CREATED->defaultName()
            ]);
        }

        return $model;
    }

    public function scopeActive($query)
    {
        return $query->whereActive(true);
    }
}
