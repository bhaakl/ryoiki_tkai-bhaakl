<?php

use App\Http\Controllers\v1\App\CategoryController;
use App\Http\Controllers\v1\App\DeliveryController;
use App\Http\Controllers\v1\App\KitController;
use App\Http\Controllers\v1\App\OrderController;
use App\Http\Controllers\v1\App\OrderStatusController;
use App\Http\Controllers\v1\App\PaymentController;
use App\Http\Controllers\v1\App\PaymentHandlerController;
use App\Http\Controllers\v1\App\ProductController;
use App\Http\Controllers\v1\App\StoreController;
use App\Http\Middleware\IsCompanySet;
use App\Http\Middleware\LogRequest;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => [IsCompanySet::class, LogRequest::class]], function () {
    Route::group(['prefix' => 'stores'], function () {
        Route::get('', [StoreController::class, 'index']);
    });
    Route::group(['prefix' => 'categories'], function () {
        Route::get('', [CategoryController::class, 'index']);
        Route::get('{category}', [CategoryController::class, 'show'])->whereNumber('category');
        Route::get('{category}/detail', [CategoryController::class, 'showDetail'])->whereNumber('category');
    });
    Route::group(['prefix' => 'products'], function () {
        Route::get('', [ProductController::class, 'index']);
        Route::get('ids', [ProductController::class, 'getByIds']);
        Route::get('search', [ProductController::class, 'search']);
        Route::get('filters', [ProductController::class, 'geFilters']);
        Route::get('properties/{property}', [ProductController::class, 'getProperty'])->whereNumber('property');
        Route::post('refresh', [ProductController::class, 'refresh']);
        Route::get('{product}', [ProductController::class, 'show'])->whereNumber('product');
        Route::get('{product}/description', [ProductController::class, 'showDescription'])->whereNumber('product');
        Route::get('{product}/characteristics', [ProductController::class, 'showCharacteristics'])->whereNumber('product');
        Route::get('{product}/components', [ProductController::class, 'showComponents'])->whereNumber('product');
        Route::get('{product}/similar', [ProductController::class, 'showSimilar'])->whereNumber('product');
    });
    Route::group(['prefix' => 'kits'], function () {
        Route::get('', [KitController::class, 'index']);
    });
    Route::group(['prefix' => 'deliveries'], function () {
        Route::get('', [DeliveryController::class, 'index']);
        Route::get('{delivery}/stores', [DeliveryController::class, 'getStores'])->whereNumber('delivery');
        Route::get('{delivery}/calendar', [DeliveryController::class, 'getCalendar'])->whereNumber('delivery');
        Route::group(['prefix' => 'chief'], function () {
            Route::get('types', [DeliveryController::class, 'getDeliveryTypes']);
            Route::get('icons', [DeliveryController::class, 'getDeliveryIcons']);
            Route::get('', [DeliveryController::class, 'indexByChief']);
            Route::post('', [DeliveryController::class, 'storeByChief']);
            Route::get('{delivery}', [DeliveryController::class, 'showByChief'])->whereNumber('delivery');
            Route::put('{delivery}', [DeliveryController::class, 'updateByChief'])->whereNumber('delivery');
            Route::delete('{delivery}', [DeliveryController::class, 'destroyByChief'])->whereNumber('delivery');
        });
    });
    Route::group(['prefix' => 'orders'], function () {
        Route::get('', [OrderController::class, 'index']);
        Route::get('latest', [OrderController::class, 'latest']);
        Route::get('{order}', [OrderController::class, 'show'])->whereNumber('order');
        Route::get('{order}/cancel', [OrderController::class, 'cancel'])->whereNumber('order');
        Route::post('create', [OrderController::class, 'store']);
        Route::post('one-click', [OrderController::class, 'oneClick']);
        Route::get('statuses', [OrderController::class, 'orderStatusList']);
    });
    Route::get('order-statuses', [OrderStatusController::class]);
    Route::group(['prefix' => 'payments'], function () {
        Route::any('webhook/{tenant}', [PaymentHandlerController::class, 'webhook'])->whereNumber('tenant')->withoutMiddleware(IsCompanySet::class);
        Route::any('success/{tenant}', [PaymentHandlerController::class, 'success'])->whereNumber('tenant')->withoutMiddleware(IsCompanySet::class);
        Route::any('fail/{tenant}', [PaymentHandlerController::class, 'fail'])->whereNumber('tenant')->withoutMiddleware(IsCompanySet::class);
        Route::post('receipt/callback/{tenant}', [PaymentHandlerController::class, 'receiptCallback'])->whereNumber('tenant')->withoutMiddleware(IsCompanySet::class);
        Route::get('orders/{order}/pay', [PaymentController::class, 'pay'])->whereNumber('order');
        Route::get('orders/{order}/pay/cryptogram', [PaymentController::class, 'payWithCryptogram'])->whereNumber('order');
        Route::get('orders/{order}/state', [PaymentController::class, 'getState'])->whereNumber('order');
    });
});
