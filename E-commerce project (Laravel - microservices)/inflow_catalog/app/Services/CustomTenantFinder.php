<?php

namespace App\Services;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class CustomTenantFinder extends TenantFinder
{

    public function findForRequest(Request $request): ?IsTenant
    {
        $uuid = $request->header('tenant-uuid');

        return app(IsTenant::class)::whereUuid($uuid)->first();
    }
}
