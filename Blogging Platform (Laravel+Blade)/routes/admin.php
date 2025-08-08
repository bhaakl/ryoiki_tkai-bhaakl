<?php

use App\Http\Controllers\Admin\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class);
Route::resource('users', UserController::class)->only(['index', 'edit', 'update']);