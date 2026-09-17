<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('reseller.welcome'))->name('storefront.welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'showLogin'])->name('storefront.login');
    Route::post('/login', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'login'])->name('storefront.login.attempt');
    Route::get('/register', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'showRegister'])->name('storefront.register');
    Route::post('/register', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'register'])->name('storefront.register.attempt');
});
Route::post('/logout', [\App\Http\Controllers\Reseller\StorefrontAuthController::class, 'logout'])
    ->middleware('auth:web')
    ->name('storefront.logout');

/*
|--------------------------------------------------------------------------
| Storefront customer self-service — dashboard, orders, wallet, profile.
| Deliberately separate controllers from App\Http\Controllers\Reseller\*,
| which is the RESELLER OWNER's admin panel (different money flow — see
| App\Http\Controllers\Storefront\OrderController's class doc). Registered
| here (not routes/reseller.php) so these ONLY exist on the reseller's
| domain, never on the main app domain.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'storefront.customer'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Storefront\DashboardController::class, 'index'])->name('storefront.dashboard');

    Route::get('/orders/r', [\App\Http\Controllers\Storefront\OrderController::class, 'index'])->name('storefront.orders.index');
    Route::get('/orders/new', [\App\Http\Controllers\Storefront\OrderController::class, 'create'])->name('storefront.orders.create');
    Route::get('/orders/countries', [\App\Http\Controllers\Storefront\OrderController::class, 'countries'])->name('storefront.orders.countries');
    Route::get('/orders/services', [\App\Http\Controllers\Storefront\OrderController::class, 'services'])->name('storefront.orders.services');
    Route::post('/orders', [\App\Http\Controllers\Storefront\OrderController::class, 'store'])->name('storefront.orders.store');
    Route::get('/orders/{order}', [\App\Http\Controllers\Storefront\OrderController::class, 'show'])->name('storefront.orders.show');

    Route::get('/wallet', [\App\Http\Controllers\Storefront\WalletController::class, 'index'])->name('storefront.wallet.index');
    Route::post('/wallet/topup', [\App\Http\Controllers\Storefront\WalletController::class, 'fund'])->name('storefront.wallet.topup');
    Route::get('/wallet/topup-status', [\App\Http\Controllers\Storefront\WalletController::class, 'topupStatus'])->name('storefront.wallet.topup-status');

    Route::get('/profile', [\App\Http\Controllers\Storefront\ProfileController::class, 'edit'])->name('storefront.profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Storefront\ProfileController::class, 'update'])->name('storefront.profile.update');
});

/*
|--------------------------------------------------------------------------
| Reseller OWNER's panel-management routes — moved here (from
| routes/reseller.php) because the "Panel Settings / Service Pricing /
| My Customers / Revenue Summary" links live in the storefront nav
| dropdown, which only ever renders on this (subdomain) domain. Names
| are kept as reseller.manage.* so existing route() calls in views don't
| need to change. Controllers are untouched — still App\Http\Controllers\Reseller\*.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'account.active', 'reseller'])
    ->name('reseller.')
    ->group(function () {
        Route::get('/manage', [\App\Http\Controllers\Reseller\DashboardController::class, 'index'])->name('manage.index');
        Route::get('/manage/customers', [\App\Http\Controllers\Reseller\CustomerController::class, 'index'])->name('manage.customers');
        Route::get('/manage/settings', [\App\Http\Controllers\Reseller\SettingsController::class, 'edit'])->name('manage.settings');
        Route::put('/manage/settings', [\App\Http\Controllers\Reseller\SettingsController::class, 'update'])->name('manage.settings.update');
        Route::get('/manage/services', [\App\Http\Controllers\Reseller\PricingController::class, 'edit'])->name('manage.services');
        Route::put('/manage/services', [\App\Http\Controllers\Reseller\PricingController::class, 'update'])->name('manage.services.update');
        Route::get('/manage/revenue', [\App\Http\Controllers\Reseller\RevenueController::class, 'index'])->name('manage.revenue');
        Route::get('/manage/withdraw', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'create'])->name('manage.withdraw');
        Route::post('/manage/withdraw/resolve-account', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'resolveAccount'])->name('manage.withdraw.resolve-account');
        Route::post('/manage/withdraw', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'store'])->name('manage.withdraw.store');
    });