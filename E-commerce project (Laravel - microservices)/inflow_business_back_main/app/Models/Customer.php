<?php

namespace App\Models;

use App\Enums\LoyaltyTypes;
use App\Filters\QueryFilter;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property $id
 * @property $name
 * @property $email
 * @property $phone
 * @property $birthday
 * @property $push_notifications
 * @property $email_verified_at
 * @property $phone_verified_at
 */
class Customer extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, UsesTenantConnection, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'birthday',
        'password',
        'email_verified_at',
        'phone_verified_at',
        'push_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'push_notifications' => 'boolean'
    ];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getBonusBalance($version = 'v1')
    {
        /** @var AppSetting $appSetting */
        $appSetting = AppSetting::first();
        if (!$appSetting || !$appSetting->getBonusEnabled()) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'tenant-uuid' => $appSetting->loyalty_uuid
            ])
                ->get(config('loyalty.url') . '/' . $version . "/customers/$this->id/balance");
            if ($response->failed()) {
                Log::debug('getBonusBalance error. return null');
                return null;
            }
        } catch (\Exception $exception) {
            Log::debug('getBonusBalance connection error. return null');
            return null;
        }

        Log::debug('getBonusBalance success. return ' . $response->body());

        return (int)($response->body());
    }

    public function getBonusInfo($version = 'v1')
    {
        /** @var AppSetting $appSetting */
        $appSetting = AppSetting::first();
        if (!$appSetting || !$appSetting->getBonusEnabled()) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'tenant-uuid' => $appSetting->loyalty_uuid
            ])
                ->get(config('loyalty.url') . '/' . $version . "/customers/$this->id/bonus-info");
            if ($response->failed()) {
                Log::debug('getBonusInfo error. return null');
                return null;
            }
        } catch (\Exception $exception) {
            Log::debug('getBonusInfo connection error. return null');
            return null;
        }

        Log::debug('getBonusInfo success. return ' . $response->body());

        return $response->object();
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }
}
