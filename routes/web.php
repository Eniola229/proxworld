<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\WelcomeModalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / marketing
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('welcome');

Route::get('/r/{code}', function (string $code) {
    session(['referral_code' => $code]);

    return redirect()->route('register', ['ref' => $code]);
})->name('referral.capture');

Route::view('/faq', 'legal.faq')->name('faq');
Route::view('/terms-of-use', 'legal.terms-of-use')->name('terms-of-use');
Route::view('/refund-policy', 'legal.refund-policy')->name('refund-policy');
Route::view('/privacy-policy', 'legal.privacy-policy')->name('privacy-policy');
Route::view('/acceptable-use-policy', 'legal.acceptable-use-policy')->name('acceptable-use-policy');
Route::view('/reseller-agreement', 'legal.reseller-agreement')->name('reseller-agreement');
Route::view('/cookie-policy', 'legal.cookie-policy')->name('cookie-policy');

Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{newsletter:slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Flutterwave server-to-server webhook — no auth, verified by signature header instead (see FlutterwaveController::webhook).
Route::post('/wallet/flutterwave-webhook', [\App\Http\Controllers\FlutterwaveController::class, 'webhook'])->name('flutterwave.webhook');

/*
|--------------------------------------------------------------------------
| Guest-only auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.store');

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth:web')->name('logout');

Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'resend'])
    ->middleware(['auth:web', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Authenticated customer area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'account.active'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/welcome-modal/dismiss', [WelcomeModalController::class, 'dismiss'])->name('welcome-modal.dismiss');

    // Orders
    Route::get('/order/new', [\App\Http\Controllers\OrderController::class, 'create'])->name('order.create');
    Route::post('/order', [\App\Http\Controllers\OrderController::class, 'store'])->middleware('sufficient.balance')->name('order.store');
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/check-status', [\App\Http\Controllers\OrderController::class, 'checkStatus'])->name('orders.check-status');

    // Wallet
    Route::get('/wallet', [\App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/topup', [\App\Http\Controllers\WalletController::class, 'fund'])->name('wallet.topup');
    Route::get('/wallet/callback', [\App\Http\Controllers\FlutterwaveController::class, 'callback'])->name('wallet.callback');

    // API keys
    Route::get('/api', [\App\Http\Controllers\ApiKeyController::class, 'index'])->name('api.index');
    Route::post('/api/generate', [\App\Http\Controllers\ApiKeyController::class, 'store'])->name('api.generate');
    Route::post('/api/{apiKey}/toggle', [\App\Http\Controllers\ApiKeyController::class, 'toggle'])->name('api.toggle');
    Route::delete('/api/{apiKey}', [\App\Http\Controllers\ApiKeyController::class, 'destroy'])->name('api.destroy');
    Route::get('/api/{apiKey}/test', [\App\Http\Controllers\ApiKeyController::class, 'test'])->name('api.test');
    Route::get('/api-docs', [\App\Http\Controllers\ApiKeyController::class, 'docs'])->name('api.docs');

    // Referral program
    Route::get('/referral', [\App\Http\Controllers\ReferralController::class, 'index'])->name('referral.index');
    Route::get('/referral/withdraw', [\App\Http\Controllers\ReferralController::class, 'withdrawForm'])->name('referral.withdraw');
    Route::post('/referral/withdraw/bank', [\App\Http\Controllers\ReferralController::class, 'withdrawBank'])->name('referral.withdraw.bank');
    Route::post('/referral/withdraw/wallet', [\App\Http\Controllers\ReferralController::class, 'withdrawToWallet'])->name('referral.withdraw.wallet');

    // Become a reseller — application + self-service settings, all under
    // the customer's own account (no separate reseller login).
    Route::get('/reseller-panel', [\App\Http\Controllers\ResellerApplicationController::class, 'index'])->name('reseller-panel.index');
    Route::get('/reseller-panel/create', [\App\Http\Controllers\ResellerApplicationController::class, 'create'])->name('reseller-panel.create');
    Route::post('/reseller-panel', [\App\Http\Controllers\ResellerApplicationController::class, 'store'])->name('reseller-panel.store');
    Route::put('/reseller-panel', [\App\Http\Controllers\ResellerApplicationController::class, 'update'])->name('reseller-panel.update');
    Route::get('/reseller-panel/services', [\App\Http\Controllers\Reseller\PricingController::class, 'edit'])->name('reseller-panel.services');
    Route::put('/reseller-panel/services', [\App\Http\Controllers\Reseller\PricingController::class, 'update'])->name('reseller-panel.services.update');
    Route::put('/reseller-panel/domain', [\App\Http\Controllers\ResellerApplicationController::class, 'updateDomain'])->name('reseller-panel.update-domain');
    Route::post('/reseller-panel/domain/verify', [\App\Http\Controllers\ResellerApplicationController::class, 'verifyDomain'])->name('reseller-panel.verify-domain');

    // Support tickets — AJAX live chat, no attachments (Telegram for images).
    Route::get('/support', [\App\Http\Controllers\TicketController::class, 'index'])->name('support.index');
    Route::post('/support', [\App\Http\Controllers\TicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('support.show');
    Route::get('/support/{ticket}/messages', [\App\Http\Controllers\TicketController::class, 'messages'])->name('support.fetch-messages');
    Route::post('/support/{ticket}/reply', [\App\Http\Controllers\TicketController::class, 'sendMessage'])->name('support.reply');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.mark.read');
    Route::get('/notifications/settings', [\App\Http\Controllers\NotificationController::class, 'settings'])->name('notifications.settings');
});
