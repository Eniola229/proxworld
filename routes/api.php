<?php

use Illuminate\Support\Facades\Route;

// Mounted at /api/v1 with name prefix "api." by bootstrap/app.php.
// Auth is the custom ApiKey model (see App\Http\Middleware\AuthenticateApiKey),
// not Sanctum — matches the "API Keys" admin UI which always displays the
// full key value, and keeps this surface fully independent of session/guard
// auth entirely (stateless, rate-limited, ability-scoped).

Route::middleware(['throttle:60,1', 'api_key'])->group(function () {
    Route::get('/balance', [\App\Http\Controllers\Api\WalletApiController::class, 'balance'])->name('balance');

    Route::get('/services', [\App\Http\Controllers\Api\ServiceApiController::class, 'index'])
        ->middleware('api_key.ability:orders.read')->name('services.index');

    Route::post('/orders', [\App\Http\Controllers\Api\OrderApiController::class, 'store'])
        ->middleware(['api_key.ability:orders.create', 'sufficient.balance'])->name('orders.store');

    Route::get('/orders/{order}', [\App\Http\Controllers\Api\OrderApiController::class, 'show'])
        ->middleware('api_key.ability:orders.read')->name('orders.show');

    Route::get('/orders', [\App\Http\Controllers\Api\OrderApiController::class, 'index'])
        ->middleware('api_key.ability:orders.read')->name('orders.index');
});
