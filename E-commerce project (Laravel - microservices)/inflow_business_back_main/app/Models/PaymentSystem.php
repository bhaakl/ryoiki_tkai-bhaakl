<?php

namespace App\Models;

use App\Enums\PaymentIcons;
use App\Enums\PaymentSystems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $type
 * @property $name
 * @property $description
 * @property $icon
 * @property $platform_commission
 */
class PaymentSystem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'type',
        'name',
        'description',
        'icon',
        'platform_commission'
    ];

    protected $casts = [
        'type' => PaymentSystems::class,
        'icon' => PaymentIcons::class,
    ];
}
