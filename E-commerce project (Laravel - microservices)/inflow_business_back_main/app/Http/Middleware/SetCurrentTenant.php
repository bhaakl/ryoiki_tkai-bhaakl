<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('api')->check()) {
            /** @var Tenant $tenant */
            $tenant = auth()->user()->tenant;
            if (!$tenant) {
                return api_error('Компания не найдена', 404);
            }
            $tenant->makeCurrent();
        } elseif ($request->header('tenant-uuid')) {
            $tenant = Tenant::whereUuid($request->header('tenant-uuid'))->first();
            if (!$tenant) {
                return api_error('Компания не найдена', 404);
            }
        } else {
        return api_error('Компания не найдена', 404);
    }

        $request->merge(['city' => $request->header('city')]);

        return $next($request);
    }
}
