<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class DeliveryStore extends Pivot
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;
    public $incrementing = true;

    protected $primaryKey = "id";
    protected $fillable = [
        'delivery_id',
        'store_id'
    ];
}
