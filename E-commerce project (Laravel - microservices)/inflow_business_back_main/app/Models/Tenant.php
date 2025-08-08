<?php

namespace App\Models;

use App\Services\RabbitMQService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant as TenantModel;

/**
 * @property $id
 * @property $name
 * @property $phone
 * @property $email
 * @property $domain
 * @property $database
 * @property $uuid
 * @property $address
 */
class Tenant extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'domain',
        'database',
        'uuid',
        'address'
    ];

    protected static function booted()
    {
        static::creating(function (Tenant $tenant) {
            $query = "CREATE DATABASE IF NOT EXISTS $tenant->database";
            DB::statement($query);
        });

        static::created(function (Tenant $tenant) {
            $tenant->makeCurrent();
            Artisan::call("migrate --database=tenant --seed --seeder=TenantDatabaseSeeder");
            $rabbit = new RabbitMQService();
            $rabbit->createTenant($tenant);
            AppSetting::factory()->create();
        });
    }
}
