<?php

use Illuminate\Support\Facades\Route;

// Mounted at /reseller with name prefix "reseller." by bootstrap/app.php,
// under the customer `web` guard — a reseller owner is just a User, no
// separate login. Requires an approved, non-suspended Reseller record.

Route::middleware(['auth:web', 'account.active'])->group(function () {
    Route::get('/pending', [\App\Http\Controllers\Reseller\StatusController::class, 'pending'])->name('pending');

    Route::middleware('reseller')->group(function () {
        Route::get('/', [\App\Http\Controllers\Reseller\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/manage', [\App\Http\Controllers\Reseller\DashboardController::class, 'index'])->name('manage.index');
        Route::get('/manage/customers', [\App\Http\Controllers\Reseller\CustomerController::class, 'index'])->name('manage.customers');
        Route::get('/manage/settings', [\App\Http\Controllers\Reseller\SettingsController::class, 'edit'])->name('manage.settings');
        Route::put('/manage/settings', [\App\Http\Controllers\Reseller\SettingsController::class, 'update'])->name('manage.settings.update');
        Route::get('/manage/services', [\App\Http\Controllers\Reseller\PricingController::class, 'edit'])->name('manage.services');
        Route::put('/manage/services', [\App\Http\Controllers\Reseller\PricingController::class, 'update'])->name('manage.services.update');
        Route::get('/manage/revenue', [\App\Http\Controllers\Reseller\RevenueController::class, 'index'])->name('manage.revenue');
        Route::get('/manage/withdraw', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'create'])->name('manage.withdraw')
        ;
        Route::post('/manage/withdraw/resolve-account', [WithdrawalController::class, 'resolveAccount'])
        ->name('manage.withdraw.resolve-account');
        Route::post('/manage/withdraw', [\App\Http\Controllers\Reseller\WithdrawalController::class, 'store'])->name('manage.withdraw.store');

        Route::get('/order/new', [\App\Http\Controllers\Reseller\OrderController::class, 'create'])->name('order.create');
        Route::post('/order', [\App\Http\Controllers\Reseller\OrderController::class, 'store'])->name('order.store');
        Route::get('/orders', [\App\Http\Controllers\Reseller\OrderController::class, 'index'])->name('order.index');

        Route::get('/wallet', [\App\Http\Controllers\Reseller\WalletController::class, 'index'])->name('wallet.index');
        Route::post('/wallet/topup', [\App\Http\Controllers\Reseller\WalletController::class, 'fund'])->name('wallet.topup');
        Route::get('/wallet/topup-status', [\App\Http\Controllers\Reseller\WalletController::class, 'topupStatus'])->name('wallet.topup-status');

        Route::get('/profile', [\App\Http\Controllers\Reseller\ProfileController::class, 'edit'])->name('profile.index');
    });
});

// White-label storefront routes (subdomain-based) live in routes/storefront.php,
// registered separately in bootstrap/app.php — they must NOT sit under this
// file's /reseller path prefix or reseller. name prefix.
