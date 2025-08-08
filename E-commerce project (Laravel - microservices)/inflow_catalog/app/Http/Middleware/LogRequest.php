<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('log request', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->all(),
        ]);

        $request->headers->set('Accept', 'application/json');
        $response = $next($request);

        Log::info('log response', [
            'response decoded' => Str::words($response->getContent(), 500),
        ]);

        return $response;
    }
}
