<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| White-label storefront (a reseller's own end customers)
|--------------------------------------------------------------------------
| Subdomain-based, resolved via App\Http\Middleware\ResolveResellerFromSubdomain
| (shares $reseller with every storefront view). End customers are regular
| platform Users; their orders are simply tagged reseller_id.
|
| Registered directly in bootstrap/app.php with no path prefix / name
| prefix (unlike routes/reseller.php, the reseller's own dashboard) so a
| storefront lives at the subdomain root — subdomain.domain/login — not
| subdomain.domain/reseller/login.
*/
Route::domain('{subdomain}.'.(config('proxworld.base_domain') ?: (parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost')))
    ->middleware('storefront')
    ->group(function () {
        Route::get('/', fn () => view('reseller.welcome'))->name('storefront.welcome');

        // Reseller-branded auth — its own controller/views, separate from
        // the main site's Auth\LoginController/RegisterController, so a
        // reseller's storefront login page carries their own panel_name
        // and colours instead of the main ProxWorld login screen. Still
        // authenticates against the same `web` guard/users table — a
        // storefront customer is a regular User, just tagged reseller_id.
        Route::middleware('guest')->group(function () {
            Route::get('/login', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'showLogin'])->name('storefront.login');
            Route::post('/login', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'login'])->name('storefront.login.attempt');
            Route::get('/register', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'showRegister'])->name('storefront.register');
            Route::post('/register', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'register'])->name('storefront.register.attempt');
        });
        Route::post('/logout', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'logout'])
            ->middleware('auth:web')
            ->name('storefront.logout');
    });
