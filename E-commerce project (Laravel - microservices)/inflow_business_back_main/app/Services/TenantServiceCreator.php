<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

class TenantServiceCreator
{
    public function new(User $user)
    {
        $tenant = Tenant::create([
            'phone' => $user->phone,
            'email' => $user->email,
            'database' => "tenant_{$user->id}",
            'uuid' => Str::uuid()->toString()
        ]);

        $user->update(['tenant_id' => $tenant->id]);
    }
}
