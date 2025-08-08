<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $network
 * @property $link
 * @property $android_link
 * @property $ios_link
 */
class SocialLink extends Model
{
    use HasFactory, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'network',
        'link',
        'android_link',
        'ios_link'
    ];
}
