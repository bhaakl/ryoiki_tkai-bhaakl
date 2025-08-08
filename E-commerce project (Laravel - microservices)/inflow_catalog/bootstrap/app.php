<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [__DIR__.'/../routes/api/api_v1.php', __DIR__.'/../routes/api/dashboard_v1.php'],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AccessDeniedHttpException $exception) {
            return response()->json([
                'success' => 0,
                'error' => $exception->getMessage(),
                'data' => null,
            ], $exception->getStatusCode());
        });
        $exceptions->render(function (NotFoundHttpException $exception) {
            return response()->json([
                'success' => 0,
                'error' => $exception->getMessage(),
                'data' => null,
            ], $exception->getStatusCode());
        });
    })->create();
