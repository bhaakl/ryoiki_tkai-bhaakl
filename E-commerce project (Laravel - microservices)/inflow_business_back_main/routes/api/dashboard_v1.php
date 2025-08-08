<?php

use App\Http\Controllers\Api\v1\Dashboard\AboutController;
use App\Http\Controllers\Api\v1\Dashboard\AcquiringController;
use App\Http\Controllers\Api\v1\Dashboard\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\v1\Dashboard\AppSettingController;
use App\Http\Controllers\Api\v1\Dashboard\ArticleController;
use App\Http\Controllers\Api\v1\Dashboard\AuthController as ChiefAuthController;
use App\Http\Controllers\Api\v1\Dashboard\BannerController;
use App\Http\Controllers\Api\v1\Dashboard\CategoryController;
use App\Http\Controllers\Api\v1\Dashboard\ContactInfoController;
use App\Http\Controllers\Api\v1\Dashboard\ComponentController;
use App\Http\Controllers\Api\v1\Dashboard\CustomerController;
use App\Http\Controllers\Api\v1\Dashboard\DeliveryController;
use App\Http\Controllers\Api\v1\Dashboard\LoyaltyController;
use App\Http\Controllers\Api\v1\Dashboard\MainPageController;
use App\Http\Controllers\Api\v1\Dashboard\MarketController;
use App\Http\Controllers\Api\v1\Dashboard\MediaController;
use App\Http\Controllers\Api\v1\Dashboard\MenuItemController;
use App\Http\Controllers\Api\v1\Dashboard\NavBarItemController;
use App\Http\Controllers\Api\v1\Dashboard\OrderController;
use App\Http\Controllers\Api\v1\Dashboard\PasswordResetController as ChiefPasswordResetController;
use App\Http\Controllers\Api\v1\Dashboard\PaymentSystemController;
use App\Http\Controllers\Api\v1\Dashboard\ProductController;
use App\Http\Controllers\Api\v1\Dashboard\ProfileController;
use App\Http\Controllers\Api\v1\Dashboard\PropertyController;
use App\Http\Controllers\Api\v1\Dashboard\SecurityController as ChiefSecurityController;
use App\Http\Controllers\Api\v1\Dashboard\StoreController;
use App\Http\Controllers\Api\v1\Dashboard\TagController;
use \App\Http\Controllers\Api\v1\Dashboard\Admin\ChiefController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\LogRequest;
use App\Http\Middleware\SetCurrentTenant;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => LogRequest::class, SetCurrentTenant::class], function () {
    Route::prefix('dashboard/v1')->group(function () {
        Route::group(['middleware' => ['auth:api', 'role:chief'], 'prefix' => 'media'], function () {
            Route::post('/upload', [MediaController::class, 'upload']);
            Route::delete('', [MediaController::class, 'delete']);
        });

        Route::prefix('chief')->group(function () {
            Route::post('registration', [ChiefAuthController::class, 'registration']);
            Route::post('login', [ChiefAuthController::class, 'login']);
            Route::get('logout', [ChiefAuthController::class, 'logout'])->middleware('auth:api');
            Route::post('auth/complete', [ChiefAuthController::class, 'complete']);
            Route::post('registration/change-phone', [ChiefAuthController::class, 'changePhone']);
            Route::post('send-code', [ChiefSecurityController::class, 'sendCode']);
            Route::post('password-reset-link', [ChiefPasswordResetController::class, 'sendLink']);
            Route::post('password-reset', [ChiefPasswordResetController::class, 'reset']);
            Route::group(['middleware' => ['auth:api', 'role:chief']], function () {
                Route::group(['prefix' => 'profile'], function () {
                    Route::get('me', [ProfileController::class, 'me']);
                });
                Route::get('/main', MainController::class);
                Route::group(['prefix' => 'payment-systems'], function () {
                    Route::get('', [PaymentSystemController::class, 'index']);
                });

                Route::prefix('contact-info')->group(function () {
                    Route::get('', [ContactInfoController::class, 'index']);
                    Route::put('', [ContactInfoController::class, 'update']);
                    Route::get('networks', [ContactInfoController::class, 'getAvailableSocialNetworks']);
                });

                Route::group(['prefix' => 'application'], function () {
                    Route::get('', [AppSettingController::class, 'show']);
                    Route::put('auth', [AppSettingController::class, 'updateAuthType']);
                    Route::put('main', [AppSettingController::class, 'updateMainSettings']);
                    Route::post('main', [AppSettingController::class, 'uploadMainMedia']);
                    Route::put('ios', [AppSettingController::class, 'updateIos']);
                    Route::put('ios/contacts', [AppSettingController::class, 'updateIosContacts']);
                    Route::post('ios', [AppSettingController::class, 'uploadIosMedia']);
                    Route::put('android', [AppSettingController::class, 'updateAndroid']);
                    Route::post('android', [AppSettingController::class, 'uploadAndroidMedia']);
                });

                Route::group(['prefix' => 'about'], function () {
                    Route::get('image', [AboutController::class, 'getMainImage']);
                    Route::post('image', [AboutController::class, 'uploadMainImage']);
                    Route::get('templates', [AboutController::class, 'templates']);
                    Route::get('', [AboutController::class, 'index']);
                    Route::get('{id}', [AboutController::class, 'show'])->whereNumber('id');
                    Route::post('', [AboutController::class, 'store']);
                    Route::put('{id}', [AboutController::class, 'update'])->whereNumber('id');
                    Route::post('{id}', [AboutController::class, 'uploadImage'])->whereNumber('id');
                    Route::delete('{id}', [AboutController::class, 'destroy'])->whereNumber('id');
                });

                Route::group(['prefix' => 'mainpage'], function () {
                    Route::get('', [MainPageController::class, 'index']);
                    Route::put('', [MainPageController::class, 'update']);
                    Route::get('templates', [MainPageController::class, 'templates']);
                    Route::group(['prefix' => 'blocks'], function () {
                        Route::get('', [MainPageController::class, 'blocks']);
                        Route::post('', [MainPageController::class, 'storeBlock']);
                        Route::put('{id}', [MainPageController::class, 'updateBlock'])->whereNumber('id');
                        Route::delete('{id}', [MainPageController::class, 'deleteBlock'])->whereNumber('id');
                        Route::post('{id}/promos', [MainPageController::class, 'storePromo'])->whereNumber('id');
                        Route::get('{id}/promos/{promo}', [MainPageController::class, 'showPromo'])->whereNumber(['id', 'promo']);
                        Route::put('{id}/promos/{promo}', [MainPageController::class, 'updatePromo'])->whereNumber(['id', 'promo']);
                        Route::post('{id}/promos/{promo}', [MainPageController::class, 'uploadImage'])->whereNumber(['id', 'promo']);
                        Route::delete('{id}/promos/{promo}', [MainPageController::class, 'destroyPromo'])->whereNumber(['id', 'promo']);
                        Route::post('{id}/products', [MainPageController::class, 'storeProduct'])->whereNumber('id');
                        Route::put('{id}/products/{product}', [MainPageController::class, 'updateProduct'])->whereNumber(['id', 'product']);
                        Route::delete('{id}/products/{product}', [MainPageController::class, 'destroyProduct'])->whereNumber(['id', 'product']);
                    });
                });

                Route::group(['prefix' => 'banners'], function () {
                    Route::get('', [BannerController::class, 'index']);
                    Route::post('', [BannerController::class, 'store']);
                    Route::put('{banner}', [BannerController::class, 'update'])->whereNumber('banner');
                    Route::post('{banner}', [BannerController::class, 'updateImage'])->whereNumber('banner');
                    Route::delete('{banner}', [BannerController::class, 'destroy'])->whereNumber('banner');
                });

                Route::group(['prefix' => 'articles'], function () {
                    Route::get('', [ArticleController::class, 'index']);
                    Route::get('{id}', [ArticleController::class, 'show'])->whereNumber('id');
                    Route::post('', [ArticleController::class, 'store']);
                    Route::put('{id}', [ArticleController::class, 'update'])->whereNumber('id');
                    Route::post('{id}', [ArticleController::class, 'updateImage'])->whereNumber('id');
                    Route::delete('{id}', [ArticleController::class, 'destroy'])->whereNumber('id');
                });

                Route::group(['prefix' => 'menu-items'], function () {
                    Route::get('icons', [MenuItemController::class, 'icons']);
                    Route::get('', [MenuItemController::class, 'index']);
                    Route::post('', [MenuItemController::class, 'store']);
                    Route::get('{item}', [MenuItemController::class, 'show'])->whereNumber('item');
                    Route::put('{item}', [MenuItemController::class, 'update'])->whereNumber('item');
                    Route::delete('{item}', [MenuItemController::class, 'destroy'])->whereNumber('item');
                });

                Route::group(['prefix' => 'navbar-items'], function () {
                    Route::get('', [NavBarItemController::class, 'index']);
                    Route::put('{item}', [NavBarItemController::class, 'update'])->whereNumber('item');
                });

                Route::group(['prefix' => 'customers'], function () {
                    Route::get('', [CustomerController::class, 'index']);
                    Route::get('dropout', [CustomerController::class, 'dropout']);
                    Route::post('', [CustomerController::class, 'store']);
                    Route::get('{id}', [CustomerController::class, 'show'])->whereNumber('id');
                    Route::put('{id}', [CustomerController::class, 'update'])->whereNumber('id');
                    Route::post('{id}/bonus', [CustomerController::class, 'addBonuses'])->whereNumber('id');
                });

                Route::group(['prefix' => 'loyalty'], function () {
                    Route::get('', [LoyaltyController::class, 'index']);
                    Route::put('', [LoyaltyController::class, 'updateLoyaltySetting']);
                    Route::group(['prefix' => 'bonus'], function () {
                        Route::post('level', [LoyaltyController::class, 'createBonusLevel']);
                        Route::put('level/{id}', [LoyaltyController::class, 'updateBonusLevel'])->whereNumber('id');
                        Route::delete('level/{id}', [LoyaltyController::class, 'deleteBonusLevel'])->whereNumber('id');
                    });
                });

                Route::group(['prefix' => 'acquiring'], function () {
                    Route::get('', [AcquiringController::class, 'show']);
                    Route::put('{id}', [AcquiringController::class, 'update'])->whereNumber('id');
                });

                Route::group(['prefix' => 'market'], function () {
                    Route::put('', [MarketController::class, 'switchMarket']);
                    Route::group(['prefix' => 'statuses'], function () {
                        Route::get('codes', [MarketController::class, 'getCodes']);
                        Route::get('', [MarketController::class, 'index']);
                        Route::get('dropout', [MarketController::class, 'dropout']);
                        Route::post('', [MarketController::class, 'store']);
                        Route::put('{id}', [MarketController::class, 'update'])->whereNumber('id');
                        Route::delete('{id}', [MarketController::class, 'destroy'])->whereNumber('id');
                    });
                    Route::group(['prefix' => 'stores'], function () {
                        Route::get('', [StoreController::class, 'index']);
                        Route::post('', [StoreController::class, 'store']);
                        Route::get('{id}', [StoreController::class, 'show'])->whereNumber('id');
                        Route::put('{id}', [StoreController::class, 'update'])->whereNumber('id');
                        Route::delete('{id}', [StoreController::class, 'destroy'])->whereNumber('id');
                    });
                    Route::group(['prefix' => 'deliveries'], function () {
                        Route::get('types', [DeliveryController::class, 'getTypes']);
                        Route::get('icons', [DeliveryController::class, 'getIcons']);
                        Route::get('', [DeliveryController::class, 'index']);
                        Route::post('', [DeliveryController::class, 'store']);
                        Route::get('{id}', [DeliveryController::class, 'show'])->whereNumber('id');
                        Route::put('{id}', [DeliveryController::class, 'update'])->whereNumber('id');
                        Route::delete('{id}', [DeliveryController::class, 'destroy'])->whereNumber('id');
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
                        Route::delete('{id}', [CategoryController::class, 'delete'])->whereNumber('id');
                    });
                    Route::group(['prefix' => 'products'], function () {
                        Route::get('', [ProductController::class, 'index']);
                        Route::get('list', [ProductController::class, 'list']);
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
                        Route::post('{id}/tags/{tag}', [ProductController::class, 'attachTag'])->whereNumber(['id', 'tag']);
                        Route::put('{id}/tags', [ProductController::class, 'syncTags'])->whereNumber('id');
                        Route::delete('{id}/tags/{tag}', [ProductController::class, 'detachTag'])->whereNumber(['id', 'tag']);
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
                        Route::get('{id}/enums', [PropertyController::class, 'getEnums'])->whereNumber('id');
                        Route::post('enums', [PropertyController::class, 'addEnum'])->whereNumber('id');
                        Route::delete('enums/{id}', [PropertyController::class, 'destroyEnum'])->whereNumber('id');
                        Route::get('strings', [PropertyController::class, 'getStrings']);
                    });
                    Route::group(['prefix' => 'tags'], function () {
                        Route::get('', [TagController::class, 'index']);
                        Route::post('', [TagController::class, 'store']);
                        Route::delete('{id}', [TagController::class, 'destroy'])->whereNumber('id');
                    });
                    Route::group(['prefix' => 'components'], function () {
                        Route::get('', [ComponentController::class, 'index']);
                        Route::post('', [ComponentController::class, 'store']);
                        Route::get('units', [ComponentController::class, 'units']);
                        Route::put('{component}', [ComponentController::class, 'update'])->whereNumber('component');
                        Route::delete('{component}', [ComponentController::class, 'destroy'])->whereNumber('component');
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
                });
            });
        });

        Route::prefix('/admin')->group(function () {
            Route::post('/registration', [AdminAuthController::class, 'registration']);
            Route::post('/login', [AdminAuthController::class, 'login']);
            Route::post('/auth/complete', [AdminAuthController::class, 'complete']);
            Route::get('/chiefs', [ChiefController::class, 'chiefs']);
            Route::get('/chief', [ChiefController::class, 'chief']);
        });
    });
});
