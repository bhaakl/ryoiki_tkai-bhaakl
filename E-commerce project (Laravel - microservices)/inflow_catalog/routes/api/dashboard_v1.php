<?php

use App\Http\Controllers\v1\Dashboard\AcquiringController;
use App\Http\Controllers\v1\Dashboard\CategoryController;
use App\Http\Controllers\v1\Dashboard\ComponentController;
use App\Http\Controllers\v1\Dashboard\DeliveryController;
use App\Http\Controllers\v1\Dashboard\OrderController;
use App\Http\Controllers\v1\Dashboard\OrderStatusController;
use App\Http\Controllers\v1\Dashboard\ProductController;
use App\Http\Controllers\v1\Dashboard\PropertyController;
use App\Http\Controllers\v1\Dashboard\StoreController;
use App\Http\Controllers\v1\Dashboard\TagController;
use App\Http\Controllers\v1\Dashboard\ShopController;
use App\Http\Middleware\LogRequest;
use App\Http\Middleware\IsCompanySet;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/dashboard', 'middleware' => [LogRequest::class]], function () {
    Route::group(['prefix' => 'acquiring'], function () {
        Route::get('', [AcquiringController::class, 'show']);
        Route::put('{id}', [AcquiringController::class, 'update'])->whereNumber('id');
    });
    Route::group(['prefix' => 'stores'], function () {
        Route::get('', [StoreController::class, 'index']);
        Route::post('', [StoreController::class, 'store']);
        Route::get('{id}', [StoreController::class, 'show'])->whereNumber('id');
        Route::put('{id}', [StoreController::class, 'update'])->whereNumber('id');
        Route::delete('{id}', [StoreController::class, 'destroy'])->whereNumber('id');
    });
    Route::group(['prefix' => 'deliveries'], function () {
        Route::get('types', [DeliveryController::class, 'getDeliveryTypes']);
        Route::get('icons', [DeliveryController::class, 'getDeliveryIcons']);
        Route::get('', [DeliveryController::class, 'index']);
        Route::post('', [DeliveryController::class, 'store']);
        Route::get('{delivery}', [DeliveryController::class, 'show'])->whereNumber('delivery');
        Route::put('{delivery}', [DeliveryController::class, 'update'])->whereNumber('delivery');
        Route::delete('{delivery}', [DeliveryController::class, 'destroy'])->whereNumber('delivery');
        Route::get('{delivery}/stores', [DeliveryController::class, 'getAvailableStores'])->whereNumber('delivery');
        Route::get('{delivery}/stores/{store}/attach', [DeliveryController::class, 'attachStore'])->whereNumber(['delivery', 'store']);
        Route::get('{delivery}/stores/{store}/detach', [DeliveryController::class, 'detachStore'])->whereNumber(['delivery', 'store']);
        Route::post('{delivery}/intervals/attach', [DeliveryController::class, 'attachInterval'])->whereNumber(['delivery']);
        Route::get('{delivery}/intervals/{interval}/detach', [DeliveryController::class, 'detachInterval'])->whereNumber(['delivery', 'interval']);
        Route::post('{delivery}/conditions/attach', [DeliveryController::class, 'attachCondition'])->whereNumber(['delivery']);
        Route::put('{delivery}/conditions/{condition}', [DeliveryController::class, 'updateCondition'])->whereNumber(['delivery', 'condition']);
        Route::get('{delivery}/conditions/{condition}/detach', [DeliveryController::class, 'detachCondition'])->whereNumber(['delivery', 'condition']);
        Route::post('{delivery}/restrictions/attach', [DeliveryController::class, 'attachRestriction'])->whereNumber(['delivery']);
        Route::put('{delivery}/restrictions/{restriction}', [DeliveryController::class, 'updateRestriction'])->whereNumber(['delivery', 'restriction']);
        Route::get('{delivery}/restrictions/{restriction}/detach', [DeliveryController::class, 'detachRestriction'])->whereNumber(['delivery', 'restriction']);
    });
    Route::group(['prefix' => 'categories'], function () {
        Route::get('', [CategoryController::class, 'index']);
        Route::get('dropout', [CategoryController::class, 'dropout']);
        Route::post('', [CategoryController::class, 'store']);
        Route::get('{id}', [CategoryController::class, 'show'])->whereNumber('id');
        Route::put('{id}', [CategoryController::class, 'update'])->whereNumber('id');
        Route::post('{id}', [CategoryController::class, 'uploadImage'])->whereNumber('id');
        Route::delete('{id}/image', [CategoryController::class, 'deleteImage'])->whereNumber('id');
        Route::delete('{id}', [CategoryController::class, 'destroy'])->whereNumber('id');
    });
    Route::group(['prefix' => 'products'], function () {
        Route::get('', [ProductController::class, 'index']);
        Route::get('list', [ProductController::class, 'dropDownList']);
        Route::post('', [ProductController::class, 'store']);
        Route::get('{id}', [ProductController::class, 'show'])->whereNumber('id');
        Route::put('{id}', [ProductController::class, 'update'])->whereNumber('id');
        Route::delete('{id}', [ProductController::class, 'destroy'])->whereNumber('id');
        Route::post('{id}/image', [ProductController::class, 'uploadImage'])->whereNumber('id');
        Route::delete('{id}/image/{uuid}', [ProductController::class, 'deleteImage'])->whereNumber('id')->whereUuid('uuid');
        Route::get('{id}/offers', [ProductController::class, 'offers'])->whereNumber('id');
        Route::post('{id}/offers', [ProductController::class, 'storeOffer'])->whereNumber('id');
        Route::get('{id}/offers/{offer}', [ProductController::class, 'showOffer'])->whereNumber(['id', 'offer']);
        Route::put('{id}/offers/{offer}', [ProductController::class, 'updateOffer'])->whereNumber(['id', 'offer']);
        Route::delete('{id}/offers/{offer}', [ProductController::class, 'destroyOffer'])->whereNumber(['id', 'offer']);
        Route::put('{id}/properties', [ProductController::class, 'updateProperties'])->whereNumber('id');
        Route::delete('{id}/properties', [ProductController::class, 'removeProperty'])->whereNumber('id');
        Route::post('{id}/tags/{tag}', [ProductController::class, 'addTag'])->whereNumber(['id', 'tag']);
        Route::put('{id}/tags', [ProductController::class, 'syncTags'])->whereNumber('id');
        Route::delete('{id}/tags/{tag}', [ProductController::class, 'removeTag'])->whereNumber(['id', 'tag']);
        Route::post('{id}/components', [ProductController::class, 'attachComponent'])->whereNumber('id');
        Route::put('{id}/components', [ProductController::class, 'syncComponents'])->whereNumber('id');
        Route::delete('{id}/components/{component}', [ProductController::class, 'detachComponent'])->whereNumber(['id', 'component']);
        Route::get('{id}/similar', [ProductController::class, 'showSimilar'])->whereNumber('id');
        Route::put('{id}/similar', [ProductController::class, 'syncSimilar'])->whereNumber('id');
        Route::delete('{id}/similar/{similar}', [ProductController::class, 'detachSimilar'])->whereNumber(['id', 'similar']);
    });
    Route::group(['prefix' => 'properties'], function () {
        Route::get('', [PropertyController::class, 'index']);
        Route::get('dropout', [PropertyController::class, 'dropout']);
        Route::post('', [PropertyController::class, 'store']);
        Route::get('{id}', [PropertyController::class, 'show'])->whereNumber('id');
        Route::put('{id}', [PropertyController::class, 'update'])->whereNumber('id');
        Route::delete('{id}', [PropertyController::class, 'destroy'])->whereNumber('id');
        Route::get('{id}/enums', [PropertyController::class, 'enumList'])->whereNumber('id');
        Route::post('enums', [PropertyController::class, 'addEnum']);
        Route::delete('enums/{id}', [PropertyController::class, 'destroyEnum'])->whereNumber('id');
        Route::get('strings', [PropertyController::class, 'getStrings']);
    });
    Route::group(['prefix' => 'tags'], function () {
        Route::get('', [TagController::class, 'index']);
        Route::post('', [TagController::class, 'store']);
        Route::delete('{id}', [TagController::class, 'destroy'])->whereNumber('id');
    });
    Route::group(['prefix' => 'components'], function () {
        Route::get('units', [ComponentController::class, 'units']);
        Route::get('', [ComponentController::class, 'index']);
        Route::post('', [ComponentController::class, 'store']);
        Route::put('{component}', [ComponentController::class, 'update'])->whereNumber('component');
        Route::delete('{component}', [ComponentController::class, 'destroy'])->whereNumber('component');
    });
    Route::group(['prefix' => 'order-statuses'], function () {
        Route::get('codes', [OrderStatusController::class, 'getCodes']);
        Route::get('', [OrderStatusController::class, 'index']);
        Route::get('dropout', [OrderStatusController::class, 'dropout']);
        Route::post('', [OrderStatusController::class, 'store']);
        Route::put('{order_status}', [OrderStatusController::class, 'update'])->whereNumber('order_status');
        Route::delete('{order_status}', [OrderStatusController::class, 'destroy'])->whereNumber('order_status');
    });
    Route::group(['prefix' => 'orders'], function () {
        Route::get('', [OrderController::class, 'index']);
        Route::get('{id}', [OrderController::class, 'show'])->whereNumber('id');
        Route::put('{id}', [OrderController::class, 'update'])->whereNumber('id');
        Route::patch('{id}', [OrderController::class, 'updateStatus'])->whereNumber('id');
        Route::post('{id}/item', [OrderController::class, 'createItem'])->whereNumber('id');
        Route::patch('{id}/item/{item}', [OrderController::class, 'updateItem'])->whereNumber(['id', 'item']);
        Route::delete('{id}/item/{item}', [OrderController::class, 'deleteItem'])->whereNumber(['id', 'item']);
    });
    Route::group(['prefix' => 'metrics', 'middleware' => [IsCompanySet::class]], function () {
        Route::get('/', [ShopController::class, 'index']);
    });
});
