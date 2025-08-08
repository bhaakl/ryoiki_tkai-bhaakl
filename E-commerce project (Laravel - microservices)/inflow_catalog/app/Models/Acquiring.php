<?php

namespace App\Models;

use App\Enums\FfdVersions;
use App\Enums\PaymentGates;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $name
 * @property $ffd
 * @property $keys
 * @property $ffd_keys
 */
class Acquiring extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'id',
        'name',
        'ffd',
        'keys',
        'ffd_keys'
    ];

    protected $casts = [
        'name' => PaymentGates::class,
        'ffd' => FfdVersions::class,
        'keys' => 'object',
        'ffd_keys' => 'object',
    ];
}
