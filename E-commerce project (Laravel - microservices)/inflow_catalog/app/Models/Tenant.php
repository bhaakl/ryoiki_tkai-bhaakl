<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant as TenantModel;

/**
 * @property $id
 * @property $database
 * @property $uuid
 */
class Tenant extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'id',
        'database',
        'uuid',
    ];

    protected static function booted()
    {
        if (app()->environment() !== 'testing') {
            static::creating(function (Tenant $tenant) {
                $query = "CREATE DATABASE IF NOT EXISTS $tenant->database";
                DB::statement($query);
            });
        }

        static::created(function (Tenant $tenant) {
            Log::debug('Tenant created ', ['tenant' => $tenant->toArray()]);
            $tenant = Tenant::latest()->first();
            $tenant->makeCurrent();
            Log::debug('Tenant created ' . $tenant->id);
            try {
                Artisan::call('db:wipe --database=tenant');
            } catch (\Exception $exception) {
                Log::debug('tenant db:wipe exception', ['exception' => $exception->getMessage()]);
            }
            Log::debug('Tenant ' . $tenant->id . ' db:wiped');
            Artisan::call("migrate --database=tenant --seed");
        });

    }
}
