<?php

use Vanguard\Http\Controllers\Web\ProductsController;
use Vanguard\Http\Controllers\Web\CartController;
use Vanguard\Http\Controllers\Web\TestAmirController;
use Vanguard\Http\Controllers\Web\PromoCodeController;
use Vanguard\Http\Controllers\Web\ReportsController;

Route::emailVerification();

Route::get('/check-admin-uploading', [
    'as' => 'check.admin.uploading',
    'uses' => 'DashboardController@checkAdminUploadingFiles'
]);

Route::get('cart', [CartController::class, 'viewCart'])->name('view.cart'); #done DB
Route::post('add-to-cart', [CartController::class, 'addToCart'])->name('add.to.cart'); #done DB
Route::patch('update-cart-qty', [CartController::class, 'updateCartQty'])->name('change.cart.qty'); #done DB
Route::post('remove-from-cart', [CartController::class, 'remove'])->name('remove.from.cart'); #done DB
Route::get('empty-cart', [CartController::class, 'emptyCart'])->name('empty.cart');#done DB
Route::get('checkout-from-cart', [CartController::class, 'checkOutCart'])->name('checkout.cart'); #done DB
Route::post('update-cart-notes', [CartController::class, 'saveOrderNotes'])->name('cart.save.notes'); #done DB

Route::post('/api/validate-cart-size', 'CartController@validateCartSelection');

Route::post('/check-popup-date', [\Vanguard\Http\Controllers\Web\SettingsController::class, 'checkPopupDate'])->name('check-popup-date');

/**
 * Authentication
 */
Route::get('login', 'Auth\LoginController@show');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('auth.logout');

Route::group(['middleware' => ['registration', 'guest']], function () {
    Route::get('register', 'Auth\RegisterController@show');
    Route::post('register', 'Auth\RegisterController@register');
});

#few routes need to do without login, and other middlewares.
Route::get('/help-faq', [
    'as' => 'help.faq.index',
    'uses' => 'HelpController@index'
]);

Route::get('/update-faq-status', [
    'as' => 'update.faq.read.status',
    'uses' => 'DashboardController@updateFaqRead'
]);

Route::group(['middleware' => ['password-reset', 'guest']], function () {
    Route::resetPassword();
});

Route::get('abc/{id}', 'TestAmirController@index2')->name('test-amir');
Route::get('test-cubes', 'TestAmirController@index3')->name('test-cubes');
Route::get('amir/{size}', 'TestAmirController@findBoxes');

/**
 * Two-Factor Authentication
 */
Route::group(['middleware' => 'two-factor'], function () {
    Route::get('auth/two-factor-authentication', 'Auth\TwoFactorTokenController@show')->name('auth.token');
    Route::post('auth/two-factor-authentication', 'Auth\TwoFactorTokenController@update')->name('auth.token.validate');
});

/**
 * Social Login
 */
Route::get('auth/{provider}/login', 'Auth\SocialAuthController@redirectToProvider')->name('social.login');
Route::get('auth/{provider}/callback', 'Auth\SocialAuthController@handleProviderCallback');

/**
 * Impersonate Routes
 */
Route::group(['middleware' => 'auth'], function () {
    Route::impersonate();
});

///Promocodes routes.
Route::middleware(['auth'])->group(function () {
    Route::get('/promo-codes', [PromoCodeController::class, 'index'])->name('promo_codes.index');
    Route::get('/promo-codes/list', [PromoCodeController::class, 'getPromoCodes'])->name('promo_codes.list'); // JSON data
    Route::post('/promo-codes/store', [PromoCodeController::class, 'store'])->name('promo_codes.store');
    Route::get('/promo-codes/{id}/edit', [PromoCodeController::class, 'edit'])->name('promo_codes.edit');
    Route::post('/promo-codes/update/{id}', [PromoCodeController::class, 'update'])->name('promo_codes.update');
    Route::delete('/promo-codes/delete/{id}', [PromoCodeController::class, 'destroy'])->name('promo_codes.destroy');
});

Route::post('/apply-promo', [CartController::class, 'applyPromoCode'])->name('apply.promo');

Route::group(['middleware' => ['auth', 'verified']], function () {

    /**
     * Dashboard
     */

    Route::get('/', 'DashboardController@index')->name('dashboard');

    Route::get('/sales-rep', [
        'as' => 'sales.rep.index',
        'uses' => 'DashboardController@salesRepInfo'
    ]);

    Route::post('/update-supplier', [
        'as' => 'update.supplier',
        'uses' => 'DashboardController@updateSupplier'
    ]);

    Route::group(['prefix' => 'products'], function () {

        #its for client ONLY
        Route::get('/inventory', [
            'as' => 'inventory.index',
            'uses' => 'ProductsController@inventoryIndex'
        ]);
        #client side ended here....!

        Route::get('/manage', [
            'as' => 'products.index.manage',
            'uses' => 'ProductsController@indexManageProducts'
        ]);

        Route::delete('/delete-product/{id}', [
            'as' => 'products.delete',
            'uses' => 'ProductsController@deleteProduct'
        ]);

        Route::delete('/reset-product/{id}', [
            'as' => 'products.reset',
            'uses' => 'ProductsController@resetProduct'
        ]);


        Route::post('/product-update-column', [
            'as' => 'product.update.column',
            'uses' => 'ProductsController@productUpdateColumn'
        ]);

        Route::post('/upload-create-products', [
            'as' => 'upload.create.products.excel',
            'uses' => 'ProductsController@uploadCreateProducts'
        ]);

        Route::post('/upload-images', [
            'as' => 'upload.products.zip.images',
            'uses' => 'ProductsController@uploadProductImages'
        ]);

        Route::post('/upload-inventory', [
            'as' => 'upload.inventory.excel',
            'uses' => 'ProductsController@uploadInventory'
        ]);

        Route::get('/inventory-reset', [
            'as' => 'inventory.reset.clear',
            'uses' => 'ProductsController@iventoryReset'
        ]);

        Route::get('/inventory-sync-ftp', [
            'as' => 'inventory.sync.ftp',
            'uses' => 'ProductsController@iventorySyncFromFTP'
        ]);

        Route::post('/copy-image', [
            'as' => 'copy.image.product',
            'uses' => 'ProductsController@copyImageToOtherProduct'
        ]);

        Route::post('/get-image-data-ajax', [
            'as' => 'get.image.data.ajax',
            'uses' => 'ProductsController@getIndexDetailsAjax'
        ]);

        Route::post('/reset-specific-inventory', [
            'as' => 'reset.specific.inventory',
            'uses' => 'ProductsController@resetSpecificInventory'
        ]);

        Route::post('/create-product', [
            'as' => 'create.product',
            'uses' => 'ProductsController@createNewProduct'
        ]);

    });

    Route::group(['prefix' => 'orders'], function () {

        Route::post('/date-carrier-validation', [
            'as' => 'date-carrier-validation',
            'uses' => 'OrdersController@dateCarrierValidation'
        ]);

        Route::get('/', [
            'as' => 'orders.index',
            'uses' => 'OrdersController@index'
        ]);

        Route::get('/order-update/{id}/{type?}', [
            'as' => 'orders.update',
            'uses' => 'OrdersController@updateOrder'
        ]);

        Route::post('/edit-order-id', [
            'as' => 'edit.order.user',
            'uses' => 'OrdersController@addOnOrderUpdate'
        ]);

        Route::post('/send-email-copy', [
            'as' => 'orders.send.email.copy',
            'uses' => 'OrdersController@sendEmailCopy'
        ]);
    });

    Route::group(['prefix' => 'boxes'], function () {
        Route::get('/', [
            'as' => 'boxes.index',
            'uses' => 'BoxesController@index'
        ]);

        Route::delete('/boxes-delete/{id}', [
            'as' => 'boxes.delete',
            'uses' => 'BoxesController@deletebox'
        ]);

        Route::post('/box-create-update', [
            'as' => 'box.create.update',
            'uses' => 'BoxesController@createAndUpdate'
        ]);

        Route::post('unit-of-measures-update', [
            'as' => 'unit_of_measures.update',
            'uses' => 'BoxesController@unitOfMeasuresUpdate'
        ]);

        Route::post('update-extra-fees-date', [
            'as' => 'update.extra.fees.date',
            'uses' => 'BoxesController@updateExtraFeesDates'
        ]);
    });

    Route::group(['prefix' => 'notifications'], function () {

        Route::get('/', [
            'as' => 'notifications.index',
            'uses' => 'Users\UsersController@indexNotifications'
        ]);

        Route::delete('/notification-delete/{id}', [
            'as' => 'notification.delete',
            'uses' => 'Users\UsersController@deleteNotifications'
        ]);
    });


    Route::group(['prefix' => 'carriers'], function () {
        Route::get('/{id?}', [
            'as' => 'carriers.index',
            'uses' => 'CarriersController@index'
        ]);

        Route::post('/carriers-create-update', [
            'as' => 'carriers.create.update',
            'uses' => 'CarriersController@createAndUpdate'
        ]);
    });

    Route::group(['prefix' => 'help-faq'], function () {
        Route::get('/edit', [
            'as' => 'help.faq.edit',
            'uses' => 'HelpController@edit'
        ]);

        Route::post('/update-help-faq', [
            'as' => 'help.faq.update',
            'uses' => 'HelpController@update'
        ]);
    });


    Route::group(['prefix' => 'shipping-address'], function () {
        Route::get('/', [
            'as' => 'shipping.address.index',
            'uses' => 'ShippingController@index'
        ]);

        Route::get('/', [
            'as' => 'shipping.address.index',
            'uses' => 'ShippingController@index'
        ]);

        Route::delete('/address-delete/{id}', [
            'as' => 'shipping.address.delete',
            'uses' => 'ShippingController@deleteAddress'
        ]);

        Route::post('/address-create-update', [
            'as' => 'ship.address.create.update',
            'uses' => 'ShippingController@createAndUpdate'
        ]);

        Route::post('/address-load-cities', [
            'as' => 'ship.address.load.cities',
            'uses' => 'ShippingController@loadCities'
        ]);

    });

    Route::get('/categories', [
        'as' => 'categories.index',
        'uses' => 'ProductsController@categoriesIndex'
    ]);

    Route::delete('/categories-delete/{id?}', [
        'as' => 'categories.delete',
        'uses' => 'ProductsController@categoriesDelete'
    ]);

    Route::post('/categories-update', [
        'as' => 'categories.update',
        'uses' => 'ProductsController@updateCategory'
    ]);

    /**
     * User Profile
     */

    Route::group(['prefix' => 'profile', 'namespace' => 'Profile'], function () {
        Route::get('/', 'ProfileController@show')->name('profile');
        Route::get('activity', 'ActivityController@show')->name('profile.activity');
        Route::put('details', 'DetailsController@update')->name('profile.update.details');

        Route::post('avatar', 'AvatarController@update')->name('profile.update.avatar');
        Route::post('avatar/external', 'AvatarController@updateExternal')
            ->name('profile.update.avatar-external');

        Route::put('login-details', 'LoginDetailsController@update')
            ->name('profile.update.login-details');

        Route::get('sessions', 'SessionsController@index')
            ->name('profile.sessions')
            ->middleware('session.database');

        Route::delete('sessions/{session}/invalidate', 'SessionsController@destroy')
            ->name('profile.sessions.invalidate')
            ->middleware('session.database');
    });
    /**
     * User Management
     */
    Route::resource('users', 'Users\UsersController')->except('update')
        ->middleware('permission:users.manage');

    Route::get('/{user}/admin-login', [
        'as' => 'user.admin.login',
        'uses' => 'Users\UsersController@adminLogin'
    ]);

    Route::post('/users/approve/{id}', [
        'as' => 'users.approve',
        'uses' => 'Users\UsersController@approve'
    ]);

    Route::group(['prefix' => 'users/{user}', 'middleware' => 'permission:users.manage'], function () {
        Route::put('update/details', 'Users\DetailsController@update')->name('users.update.details');
        Route::put('update/login-details', 'Users\LoginDetailsController@update')
            ->name('users.update.login-details');

        Route::post('update/avatar', 'Users\AvatarController@update')->name('user.update.avatar');
        Route::post('update/avatar/external', 'Users\AvatarController@updateExternal')
            ->name('user.update.avatar.external');

        Route::get('sessions', 'Users\SessionsController@index')
            ->name('user.sessions')->middleware('session.database');

        Route::delete('sessions/{session}/invalidate', 'Users\SessionsController@destroy')
            ->name('user.sessions.invalidate')->middleware('session.database');

        Route::post('two-factor/enable', 'TwoFactorController@enable')->name('user.two-factor.enable');
        Route::post('two-factor/disable', 'TwoFactorController@disable')->name('user.two-factor.disable');
    });

    /**
     * Roles & Permissions
     */
    Route::group(['namespace' => 'Authorization'], function () {
        Route::resource('roles', 'RolesController')->except('show')->middleware('permission:roles.manage');

        Route::post('permissions/save', 'RolePermissionsController@update')
            ->name('permissions.save')
            ->middleware('permission:permissions.manage');

        Route::resource('permissions', 'PermissionsController')->middleware('permission:permissions.manage');
    });

    /**
     * Activity Log
     */

    Route::get('activity', 'ActivityController@index')->name('activity.index')
        ->middleware('permission:users.activity');

    Route::get('activity/user/{user}/log', 'Users\ActivityController@index')->name('activity.user')
        ->middleware('permission:users.activity');

    /**
     * Two-Factor Authentication Setup
     */

    Route::group(['middleware' => 'two-factor'], function () {
        Route::post('two-factor/enable', 'TwoFactorController@enable')->name('two-factor.enable');

        Route::get('two-factor/verification', 'TwoFactorController@verification')
            ->name('two-factor.verification')
            ->middleware('verify-2fa-phone');

        Route::post('two-factor/resend', 'TwoFactorController@resend')
            ->name('two-factor.resend')
            ->middleware('throttle:1,1', 'verify-2fa-phone');

        Route::post('two-factor/verify', 'TwoFactorController@verify')
            ->name('two-factor.verify')
            ->middleware('verify-2fa-phone');

        Route::post('two-factor/disable', 'TwoFactorController@disable')->name('two-factor.disable');
    });


    /**
     * Settings
     */

    Route::get('settings', 'SettingsController@general')->name('settings.general')
        ->middleware('permission:settings.general');

    Route::post('settings/general', 'SettingsController@update')->name('settings.general.update')
        ->middleware('permission:settings.general');

    Route::get('settings/auth', 'SettingsController@auth')->name('settings.auth')
        ->middleware('permission:settings.auth');

    Route::post('settings/auth', 'SettingsController@update')->name('settings.auth.update')
        ->middleware('permission:settings.auth');

    if (config('services.authy.key')) {
        Route::post('settings/auth/2fa/enable', 'SettingsController@enableTwoFactor')
            ->name('settings.auth.2fa.enable')
            ->middleware('permission:settings.auth');

        Route::post('settings/auth/2fa/disable', 'SettingsController@disableTwoFactor')
            ->name('settings.auth.2fa.disable')
            ->middleware('permission:settings.auth');
    }

    Route::post('settings/auth/registration/captcha/enable', 'SettingsController@enableCaptcha')
        ->name('settings.registration.captcha.enable')
        ->middleware('permission:settings.auth');

    Route::post('settings/auth/registration/captcha/disable', 'SettingsController@disableCaptcha')
        ->name('settings.registration.captcha.disable')
        ->middleware('permission:settings.auth');

    Route::get('settings/notifications', 'SettingsController@notifications')
        ->name('settings.notifications')
        ->middleware('permission:settings.auth');

    Route::post('settings/notifications', 'SettingsController@update')
        ->name('settings.settings.auth')
        ->middleware('permission:settings.auth');

    Route::post('/notifications', [
        'as' => 'settings.notifications.update',
        'uses' => 'SettingsController@update',
        'middleware' => 'permission:settings.notifications'
    ]);


    Route::post('/generate-report', [
        'as' => 'generate.report',
        'uses' => 'ReportsController@generateReport'
    ]);
});
