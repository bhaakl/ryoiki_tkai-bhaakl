<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticateController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserPostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Маршруты API
|--------------------------------------------------------------------------
| Здесь можно зарегистрировать маршруты API для своего приложения. Эти
| маршруты загружаются провайдером RouteServiceProvider в группе, которая назначается как группой посредников «api». |
*/

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        // Posts
        Route::apiResource('posts', PostController::class)->only(['update', 'store', 'destroy']);

        // Users
        Route::apiResource('users', UserController::class)->only('update');

        // Media
        Route::apiResource('media', MediaController::class)->only(['store', 'destroy']);
    });

    Route::post('/authenticate', [AuthenticateController::class, 'authenticate'])->name('authenticate');

    // Posts
    Route::apiResource('posts', PostController::class)->only(['index', 'show']);
    Route::apiResource('users.posts', UserPostController::class)->only('index');

    // Users
    Route::apiResource('users', UserController::class)->only(['index', 'show']);

    // Media
    Route::apiResource('media', MediaController::class)->only('index');
});
