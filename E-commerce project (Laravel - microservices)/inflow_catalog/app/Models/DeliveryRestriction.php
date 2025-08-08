<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $delivery_id
 * @property $date_from
 * @property $date_to
 */
class DeliveryRestriction extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $primaryKey = "id";
    protected $fillable = [
        'id',
        'delivery_id',
        'date_from',
        'date_to'
    ];

    protected $casts = [
        'date_from' => 'date:Y-m-d',
        'date_to' => 'date:Y-m-d',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function intervals()
    {
        return $this->belongsToMany(DeliveryInterval::class, 'delivery_restriction_interval', 'delivery_restriction_id');
    }
}
