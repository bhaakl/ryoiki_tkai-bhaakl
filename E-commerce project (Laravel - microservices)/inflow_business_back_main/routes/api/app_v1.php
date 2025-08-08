<?php

use App\Http\Controllers\Api\v1\App\AboutController;
use App\Http\Controllers\Api\v1\App\AppSettingController;
use App\Http\Controllers\Api\v1\App\ArticleController;
use App\Http\Controllers\Api\v1\App\AuthController;
use App\Http\Controllers\Api\v1\App\BonusController;
use App\Http\Controllers\Api\v1\App\CategoryController;
use App\Http\Controllers\Api\v1\App\DeliveryController;
use App\Http\Controllers\Api\v1\App\FeedbackController;
use App\Http\Controllers\Api\v1\App\KitController;
use App\Http\Controllers\Api\v1\App\MainPageController;
use App\Http\Controllers\Api\v1\App\MenuItemController;
use App\Http\Controllers\Api\v1\App\OrderController;
use App\Http\Controllers\Api\v1\App\PaymentController;
use App\Http\Controllers\Api\v1\App\PaymentSystemController;
use App\Http\Controllers\Api\v1\App\ProductController;
use App\Http\Controllers\Api\v1\App\ProfileController;
use App\Http\Controllers\Api\v1\App\PromoController;
use App\Http\Controllers\Api\v1\App\SearchController;
use App\Http\Controllers\Api\v1\App\SliderController;
use App\Http\Controllers\Api\v1\App\StoreController;
use App\Http\Middleware\LogRequest;
use App\Http\Middleware\SetCurrentTenant;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'app/v1'], function () {
    Route::group(['middleware' => [SetCurrentTenant::class, LogRequest::class]], function () {
        Route::group(['prefix' => 'mainpage'], function () {
            Route::get('', [MainPageController::class, 'index']);
            Route::get('{item}', [MainPageController::class, 'showItem'])->whereNumber('item')->name('mainpage.item');
        });
        Route::get('config', [AppSettingController::class, 'getConfig']);
        Route::get('config/date', [AppSettingController::class, 'getConfigDate']);
        Route::get('payment-systems', [PaymentSystemController::class, 'index']);
        Route::get('payment-systems/{paymentSystem}', [PaymentSystemController::class, 'show'])->whereNumber('paymentSystem');
        Route::post('feedback', FeedbackController::class);
        Route::get('search', SearchController::class);
        Route::get('menu/{menuItem}', MenuItemController::class)->whereNumber('menuItem');
        Route::get('stores', StoreController::class);
        Route::get('slider', SliderController::class)->name('slider');
        Route::group(['prefix' => 'articles'], function () {
            Route::get('', [ArticleController::class, 'index'])->name('articles.index');
            Route::get('{article}', [ArticleController::class, 'show'])->whereNumber('article');
        });
        Route::group(['prefix' => 'promos'], function () {
            Route::get('{promo}', [PromoController::class, 'show'])->whereNumber('promo');
        });
        Route::group(['prefix' => 'about'], function () {
            Route::get('', [AboutController::class, 'index']);
            Route::get('{aboutItem}', [AboutController::class, 'showItem'])->whereNumber('aboutItem')->name('about.item');
        });
        Route::group(['prefix' => 'auth'], function () {
            Route::post('switch-type', [AuthController::class, 'switchAuthType']);
            Route::group(['prefix' => 'email'], function () {
                Route::post('register', [AuthController::class, 'emailRegister']);
                Route::post('confirm', [AuthController::class, 'confirmEmail']);
                Route::post('confirm/resend', [AuthController::class, 'resendConfirmationEmail']);
                Route::post('login', [AuthController::class, 'emailLogin']);
            });
            Route::group(['prefix' => 'phone'], function () {
                Route::post('login', [AuthController::class, 'phoneLogin']);
                Route::post('confirm', [AuthController::class, 'confirmPhone']);
                Route::post('confirm/resend', [AuthController::class, 'resendConfirmationSms']);
            });
            Route::post('password/reset', [AuthController::class, 'resetPassword']);
            Route::post('fill-account', [AuthController::class, 'fillAccount'])->middleware('auth:customer');
            Route::get('refresh', [AuthController::class, 'refresh'])->middleware('auth:customer');
            Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:customer');
        });
        Route::group(['middleware' => 'auth:customer'], function () {
            Route::group(['prefix' => 'profile'], function () {
                Route::get('me', [ProfileController::class, 'me']);
                Route::delete('me', [ProfileController::class, 'destroy']);
                Route::post('name', [ProfileController::class, 'updateName']);
                Route::post('birthday', [ProfileController::class, 'updateBirthday']);
                Route::post('password', [ProfileController::class, 'updatePassword']);
                Route::post('email', [ProfileController::class, 'updateEmail']);
                Route::post('email/confirm', [ProfileController::class, 'confirmEmailUpdate']);
                Route::post('phone', [ProfileController::class, 'updatePhone']);
                Route::post('phone/confirm', [ProfileController::class, 'confirmPhoneUpdate']);
                Route::post('push', [ProfileController::class, 'updatePushSettings']);
                Route::post('device', [ProfileController::class, 'addDevice']);
            });
            Route::group(['prefix' => 'bonuses'], function () {
                Route::get('info', [BonusController::class, 'getInfo'])->name('bonuses.info');
                Route::get('history', [BonusController::class, 'getHistory']);
                Route::get('expiration', [BonusController::class, 'getExpiration']);
            });
        });

        Route::group(['prefix' => 'store'], function () {
            Route::group(['prefix' => 'categories'], function () {
                Route::get('', [CategoryController::class, 'index']);
                Route::get('{id}', [CategoryController::class, 'show'])->whereNumber('id');
                Route::get('{id}/detail', [CategoryController::class, 'showDetail'])->whereNumber('id');
            });
            Route::group(['prefix' => 'products'], function () {
                Route::get('', [ProductController::class, 'index']);
                Route::post('refresh', [ProductController::class, 'refresh']);
                Route::get('filters', [ProductController::class, 'getFilters']);
                Route::get('properties/{id}', [ProductController::class, 'getProperty'])->whereNumber('id');
                Route::get('{id}', [ProductController::class, 'show'])->whereNumber('id');
                Route::get('{id}/description', [ProductController::class, 'showDescription'])->whereNumber('id');
                Route::get('{id}/characteristics', [ProductController::class, 'showCharacteristics'])->whereNumber('id');
                Route::get('{id}/components', [ProductController::class, 'showComponents'])->whereNumber('id');
                Route::get('{id}/similar', [ProductController::class, 'showSimilar'])->whereNumber('id');
            });
            Route::group(['prefix' => 'kits'], function () {
                Route::get('', [KitController::class, 'index']);
            });
            Route::group(['prefix' => 'deliveries'], function () {
                Route::get('', [DeliveryController::class, 'index']);
                Route::get('{id}/stores', [DeliveryController::class, 'getStores'])->whereNumber('id');
                Route::get('{id}/calendar', [DeliveryController::class, 'getCalendar'])->whereNumber('id');
                Route::get('address/search', [DeliveryController::class, 'addressHint']);
            });
            Route::group(['middleware' => 'auth:customer'], function () {
                Route::group(['prefix' => 'orders'], function () {
                    Route::get('', [OrderController::class, 'index'])->name('order.list');
                    Route::get('{id}', [OrderController::class, 'show'])->whereNumber('id');
                    Route::get('{id}/cancel', [OrderController::class, 'cancel'])->whereNumber('id');
                    Route::post('create', [OrderController::class, 'store']);
                    Route::post('one-click', [OrderController::class, 'oneClick'])->withoutMiddleware('auth:customer');
                    Route::get('statuses', [OrderController::class, 'orderStatusList']);
                });
                Route::group(['prefix' => 'payments'], function () {
                    Route::get('order/{id}/pay', [PaymentController::class, 'pay'])->whereNumber('id');
                    Route::post('order/{id}/pay/cryptogram', [PaymentController::class, 'payWithCryptogram'])->whereNumber('id');
                    Route::get('order/{id}/state', [PaymentController::class, 'getState'])->whereNumber('id');
                });
            });
        });
    });
});
