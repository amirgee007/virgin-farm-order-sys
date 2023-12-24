<?php


use Vanguard\Http\Controllers\Web\ProductsController;

Route::get('/', [ProductsController::class, 'index']);
Route::get('cart', [ProductsController::class, 'cart'])->name('cart');
Route::post('add-to-cart', [ProductsController::class, 'addToCart'])->name('add.to.cart');
Route::patch('update-cart', [ProductsController::class, 'update'])->name('update.cart');
Route::delete('remove-from-cart', [ProductsController::class, 'remove'])->name('remove.from.cart');
Route::get('checkout-from-cart', [ProductsController::class, 'checkOutCart'])->name('checkout.cart');



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

Route::emailVerification();

Route::group(['middleware' => ['password-reset', 'guest']], function () {
    Route::resetPassword();
});

Route::get('abc', 'TestAmirController@index2')->name('test-amir');

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

Route::group(['middleware' => ['auth', 'verified']], function () {

    /**
     * Dashboard
     */

    Route::get('/', 'DashboardController@index')->name('dashboard');

    Route::group(['prefix'=>'products'], function() {
        Route::get('/', [
            'as' => 'products.index',
            'uses' => 'ProductsController@index'
        ]);

        Route::get('/inventory', [
            'as' => 'inventory.index',
            'uses' => 'ProductsController@inventoryIndex'
        ]);

        Route::post('/inventory-update-column', [
            'as' => 'inventory.update.column',
            'uses' => 'ProductsController@inventoryUpdateColumn'
        ]);

        Route::post('/upload-products', [
            'as' => 'upload.products.excel',
            'uses' => 'ProductsController@uploadProducts'
        ]);

        Route::post('/upload-images', [
            'as' => 'upload.products.zip.images',
            'uses' => 'ProductsController@uploadProductImages'
        ]);

        Route::post('/upload-inventory', [
            'as' => 'upload.inventory.excel',
            'uses' => 'ProductsController@uploadInventory'
        ]);

        Route::post('/add-to-cart', [
            'as' => 'product.add.to.cart',
            'uses' => 'ProductsController@addToCart'
        ]);


    });


    Route::group(['prefix'=>'boxes'], function() {
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
    });

    Route::group(['prefix'=>'notifications'], function() {

        Route::get('/', [
            'as' => 'notifications.index',
            'uses' => 'Users\UsersController@indexNotifications'
        ]);

        Route::delete('/notification-delete/{id}', [
            'as' => 'notification.delete',
            'uses' => 'Users\UsersController@deleteNotifications'
        ]);
    });


    Route::group(['prefix'=>'carriers'], function() {
        Route::get('/{id?}', [
            'as' => 'carriers.index',
            'uses' => 'CarriersController@index'
        ]);

        Route::post('/carriers-create-update', [
            'as' => 'carriers.create.update',
            'uses' => 'CarriersController@createAndUpdate'
        ]);
    });


    Route::group(['prefix'=>'shipping-address'], function() {
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

    });

    Route::get('/categories', [
        'as' => 'categories.index',
        'uses' => 'ProductsController@categoriesIndex'
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
});
