<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [__DIR__.'/../routes/api/app_v1.php', __DIR__.'/../routes/api/dashboard_v1.php'],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception) {
            return response()->json([
                'success' => 0,
                'error' => $exception->getMessage(),
                'data' => $exception->errors(),
            ], $exception->status);
        });
        $exceptions->render(function (AccessDeniedHttpException $exception) {
            return response()->json([
                'success' => 0,
                'error' => $exception->getMessage(),
                'data' => null,
            ], $exception->getStatusCode());
        });
        $exceptions->render(function (AuthenticationException $exception) {
            return response()->json([
                'success' => 0,
                'error' => $exception->getMessage(),
                'data' => null,
            ], 401);
        });
    })->create();
