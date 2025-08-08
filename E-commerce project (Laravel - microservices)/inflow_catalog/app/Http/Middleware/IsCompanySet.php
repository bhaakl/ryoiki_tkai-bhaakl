<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCompanySet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->header('tenant-uuid')) {
            return api_error('Компания не найдена!', 404, );
        }
        $tenant = Tenant::whereUuid($request->header('tenant-uuid'))->first();
        if (!$tenant) {
            return api_error('Компания не найдена!!', 404);
        }

        $tenant->makeCurrent();

        return $next($request);
    }
}
