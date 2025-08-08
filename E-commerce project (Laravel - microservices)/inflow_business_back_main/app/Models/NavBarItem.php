<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $value
 * @property $position
 * @property $active
 * @property $switchable
 * @property $icon
 */
class NavBarItem extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'value',
        'position',
        'active',
        'switchable',
        'icon'
    ];

    protected $casts = [
        'switchable' => 'boolean',
        'active' => 'boolean',
    ];

    public function getIconUrl()
    {
        if ($this->getAttribute('icon')) {
            return config('app.url') . $this->getAttribute('icon');
        }

        return null;
    }
}
