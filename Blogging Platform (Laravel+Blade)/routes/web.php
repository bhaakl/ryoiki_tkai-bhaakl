<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\PostFeedController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostThumbnailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web-маршруты
|--------------------------------------------------------------------------
|
| Здесь вы можете зарегистрировать веб-маршруты для вашего приложения. Эти
| маршруты загружаются провайдером RouteServiceProvider, и все они 
| будут назначены в группе middleware «web». 
|
*/

Route::resource('posts', PostController::class);
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/posts/feed', [PostFeedController::class, 'index'])->name('posts.feed');
Route::resource('posts', PostController::class)->only('show');
Route::resource('users', UserController::class)->only('show');