<?php

use App\Http\Controllers\Admin\AdminInvitationController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\PricingSettingsController;
use App\Http\Controllers\Admin\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PricingRuleController;


// Mounted at /control-panel, name prefix "admin.", admin.session + web
// middleware already applied — see bootstrap/app.php.

Route::middleware('admin.guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.post');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.store');
});

Route::get('/invitations/{invitation}/accept/{token}', [AdminInvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invitations/{invitation}/accept/{token}', [AdminInvitationController::class, 'store'])->name('invitations.store');

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('admin.auth')->name('logout');

Route::middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view,admin')->name('dashboard');

    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');

    Route::middleware('permission:reports.view,admin')->group(function () {
        Route::get('/profit', [\App\Http\Controllers\Admin\ProfitReportController::class, 'index'])
            ->middleware('permission:profit.view,admin')->name('profit.index');
    });

    Route::middleware('permission:orders.view,admin')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/check-status', [\App\Http\Controllers\Admin\OrderController::class, 'checkStatus'])
            ->middleware('permission:orders.manage,admin')->name('orders.check-status');
        Route::put('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])
            ->middleware('permission:orders.manage,admin')->name('orders.update-status');
        Route::delete('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])
            ->middleware('permission:orders.manage,admin')->name('orders.destroy');
    });

    Route::middleware('permission:customers.view,admin')->group(function () {
        Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{user}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
        Route::get('/customers/{user}/edit', [\App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{user}', [\App\Http\Controllers\Admin\CustomerController::class, 'update'])
            ->middleware('permission:customers.suspend,admin')->name('customers.update');
        Route::post('/customers/{user}/adjust-balance', [\App\Http\Controllers\Admin\CustomerController::class, 'adjustBalance'])
            ->middleware('permission:wallet.adjust,admin')->name('customers.adjust-balance');
    });

    Route::middleware('permission:resellers.view,admin')->group(function () {
        Route::get('/resellers', [\App\Http\Controllers\Admin\ResellerController::class, 'index'])->name('resellers.index');
        Route::get('/resellers/create', [\App\Http\Controllers\Admin\ResellerController::class, 'create'])->name('resellers.create');
        Route::get('/resellers/{reseller}', [\App\Http\Controllers\Admin\ResellerController::class, 'show'])->name('resellers.show');
        Route::get('/resellers/{reseller}/wallet', [\App\Http\Controllers\Admin\ResellerController::class, 'wallet'])->name('resellers.wallet');
        Route::get('/resellers/{reseller}/customers', [\App\Http\Controllers\Admin\ResellerController::class, 'customers'])->name('resellers.customers');
        Route::get('/resellers/{reseller}/orders', [\App\Http\Controllers\Admin\ResellerController::class, 'orders'])->name('resellers.orders');
        Route::get('/resellers/{reseller}/withdrawals', [\App\Http\Controllers\Admin\ResellerController::class, 'withdrawals'])->name('resellers.withdrawals');
        Route::post('/resellers/{reseller}/approve', [\App\Http\Controllers\Admin\ResellerController::class, 'approve'])
            ->middleware('permission:resellers.approve,admin')->name('resellers.approve');
        Route::post('/resellers/{reseller}/reject', [\App\Http\Controllers\Admin\ResellerController::class, 'reject'])
            ->middleware('permission:resellers.approve,admin')->name('resellers.reject');
        Route::post('/resellers/{reseller}/status', [\App\Http\Controllers\Admin\ResellerController::class, 'toggleStatus'])
            ->middleware('permission:resellers.suspend,admin')->name('resellers.status');
    });

    Route::middleware('permission:reseller-withdrawals.view,admin')->group(function () {
        Route::get('/reseller-withdrawals', [\App\Http\Controllers\Admin\ResellerWithdrawalController::class, 'index'])->name('reseller-withdrawals.index');
        Route::get('/reseller-withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\ResellerWithdrawalController::class, 'show'])->name('reseller-withdrawals.show');
        Route::post('/reseller-withdrawals/{withdrawal}/approve', [\App\Http\Controllers\Admin\ResellerWithdrawalController::class, 'approve'])
            ->middleware('permission:reseller-withdrawals.process,admin')->name('reseller-withdrawals.approve');
        Route::post('/reseller-withdrawals/{withdrawal}/reject', [\App\Http\Controllers\Admin\ResellerWithdrawalController::class, 'reject'])
            ->middleware('permission:reseller-withdrawals.process,admin')->name('reseller-withdrawals.reject');
    });

    Route::middleware('permission:referral-withdrawals.view,admin')->group(function () {
        Route::get('/referral/withdrawals', [\App\Http\Controllers\Admin\ReferralWithdrawalController::class, 'index'])->name('referral.withdrawals.index');
        Route::get('/referral/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\ReferralWithdrawalController::class, 'show'])->name('referral.withdrawals.show');
        Route::post('/referral/withdrawals/{withdrawal}/approve-bank', [\App\Http\Controllers\Admin\ReferralWithdrawalController::class, 'approveBank'])
            ->middleware('permission:referral-withdrawals.process,admin')->name('referral.withdrawals.approve-bank');
        Route::post('/referral/withdrawals/{withdrawal}/approve-wallet', [\App\Http\Controllers\Admin\ReferralWithdrawalController::class, 'approveWallet'])
            ->middleware('permission:referral-withdrawals.process,admin')->name('referral.withdrawals.approve-wallet');
        Route::post('/referral/withdrawals/{withdrawal}/reject', [\App\Http\Controllers\Admin\ReferralWithdrawalController::class, 'reject'])
            ->middleware('permission:referral-withdrawals.process,admin')->name('referral.withdrawals.reject');
    });

    Route::middleware('permission:providers.view,admin')->group(function () {
        Route::get('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'index'])->name('providers.index');
        Route::get('/providers/create', [\App\Http\Controllers\Admin\ProviderController::class, 'create'])
            ->middleware('permission:providers.manage,admin')->name('providers.create');
        Route::post('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'store'])
            ->middleware('permission:providers.manage,admin')->name('providers.store');
        Route::get('/providers/{provider}/edit', [\App\Http\Controllers\Admin\ProviderController::class, 'edit'])
            ->middleware('permission:providers.manage,admin')->name('providers.edit');
        Route::put('/providers/{provider}', [\App\Http\Controllers\Admin\ProviderController::class, 'update'])
            ->middleware('permission:providers.manage,admin')->name('providers.update');
        Route::delete('/providers/{provider}', [\App\Http\Controllers\Admin\ProviderController::class, 'destroy'])
            ->middleware('permission:providers.manage,admin')->name('providers.destroy');
        Route::post('/providers/{provider}/toggle', [\App\Http\Controllers\Admin\ProviderController::class, 'toggle'])
            ->middleware('permission:providers.manage,admin')->name('providers.toggle');
        Route::post('/providers/{provider}/refresh-balance', [\App\Http\Controllers\Admin\ProviderController::class, 'refreshBalance'])
            ->middleware('permission:providers.manage,admin')->name('providers.refresh-balance');
        Route::post('/providers/refresh-all', [\App\Http\Controllers\Admin\ProviderController::class, 'refreshAll'])
            ->middleware('permission:providers.manage,admin')->name('providers.refresh-all');
    });

    Route::middleware('permission:pricing.view,admin')->group(function () {
        Route::get('/settings/pricing', [PricingSettingsController::class, 'edit'])->name('settings.pricing.index');
        Route::post('/settings/pricing', [PricingSettingsController::class, 'update'])
            ->middleware('permission:pricing.manage,admin')->name('settings.pricing.update');
        Route::post('/pricing-rules', [PricingRuleController::class, 'store'])
            ->middleware('permission:pricing.manage,admin')->name('pricing-rules.store');
        Route::post('/pricing-rules/{rule}/toggle', [PricingRuleController::class, 'toggle'])
            ->middleware('permission:pricing.manage,admin')->name('pricing-rules.toggle');
        Route::delete('/pricing-rules/{rule}', [PricingRuleController::class, 'destroy'])
            ->middleware('permission:pricing.manage,admin')->name('pricing-rules.destroy');
    });

    Route::middleware('permission:currencies.manage,admin')->group(function () {
        Route::get('/exchange-rates', [\App\Http\Controllers\Admin\ExchangeRateController::class, 'index'])->name('exchange-rates.index');
        Route::post('/exchange-rates/refresh', [\App\Http\Controllers\Admin\ExchangeRateController::class, 'refresh'])->name('exchange-rates.refresh');
        Route::post('/exchange-rates/refresh-all', [\App\Http\Controllers\Admin\ExchangeRateController::class, 'refreshAll'])->name('exchange-rates.refresh-all');
    });

    Route::middleware('permission:wallet.view,admin')->group(function () {
        Route::get('/wallet', [\App\Http\Controllers\Admin\WalletController::class, 'index'])->name('wallet.index');
        Route::get('/wallet/{transaction}', [\App\Http\Controllers\Admin\WalletController::class, 'show'])->name('wallet.show');
        Route::post('/wallet/{transaction}/approve', [\App\Http\Controllers\Admin\WalletController::class, 'approve'])
            ->middleware('permission:wallet.adjust,admin')->name('wallet.approve');
        Route::post('/wallet/{transaction}/reject', [\App\Http\Controllers\Admin\WalletController::class, 'reject'])
            ->middleware('permission:wallet.adjust,admin')->name('wallet.reject');
        Route::delete('/wallet/{transaction}', [\App\Http\Controllers\Admin\WalletController::class, 'destroy'])
            ->middleware('permission:wallet.adjust,admin')->name('wallet.destroy');
    });

    Route::middleware('permission:tickets.view,admin')->group(function () {
        Route::get('/support', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('support.index');
        Route::get('/support/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->name('support.show');
        Route::get('/support/{ticket}/messages', [\App\Http\Controllers\Admin\TicketController::class, 'messages'])->name('support.fetch-messages');
        Route::post('/support/{ticket}/reply', [\App\Http\Controllers\Admin\TicketController::class, 'sendMessage'])
            ->middleware('permission:tickets.manage,admin')->name('support.reply');
        Route::post('/support/{ticket}/close', [\App\Http\Controllers\Admin\TicketController::class, 'close'])
            ->middleware('permission:tickets.manage,admin')->name('support.close');
        Route::post('/support/{ticket}/reopen', [\App\Http\Controllers\Admin\TicketController::class, 'reopen'])
            ->middleware('permission:tickets.manage,admin')->name('support.reopen');
        Route::post('/support/{ticket}/status', [\App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])
            ->middleware('permission:tickets.manage,admin')->name('support.status');
        Route::delete('/support/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'destroy'])
            ->middleware('permission:tickets.manage,admin')->name('support.destroy');
    });

    Route::middleware('permission:newsletter.view,admin')->group(function () {
        Route::get('/newsletters', [\App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletters.index');

        // Create & Store — MUST come before the {newsletter} show route below,
        // or "create" gets swallowed as the {newsletter} wildcard and 404s.
        Route::get('/newsletters/create', [\App\Http\Controllers\Admin\NewsletterController::class, 'create'])
            ->middleware('permission:newsletter.manage,admin')->name('newsletters.create');
        Route::post('/newsletters', [\App\Http\Controllers\Admin\NewsletterController::class, 'store'])
            ->middleware('permission:newsletter.manage,admin')->name('newsletters.store');

        // View/Show route
        Route::get('/newsletters/{newsletter}', [\App\Http\Controllers\Admin\NewsletterController::class, 'show'])->name('newsletters.show');

        // Edit & Update
        Route::get('/newsletters/{newsletter}/edit', [\App\Http\Controllers\Admin\NewsletterController::class, 'edit'])
            ->middleware('permission:newsletter.manage,admin')->name('newsletters.edit');
        Route::put('/newsletters/{newsletter}', [\App\Http\Controllers\Admin\NewsletterController::class, 'update'])
            ->middleware('permission:newsletter.manage,admin')->name('newsletters.update');

        // Delete
        Route::delete('/newsletters/{newsletter}', [\App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])
            ->middleware('permission:newsletter.manage,admin')->name('newsletters.destroy');

        // Actions & Media
        Route::post('/newsletters/{newsletter}/send', [\App\Http\Controllers\Admin\NewsletterController::class, 'send'])
            ->middleware('permission:newsletter.send,admin')->name('newsletters.send');
        Route::post('/newsletters/media', [\App\Http\Controllers\Admin\NewsletterController::class, 'uploadMedia'])
            ->middleware('permission:newsletter.manage,admin')->name('newsletters.media');
    });
    
    Route::middleware('permission:admins.view,admin')->group(function () {
        Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
        Route::get('/admins/create', [AdminManagementController::class, 'create'])
            ->middleware('permission:admins.manage,admin')->name('admins.create');
        Route::post('/admins', [AdminManagementController::class, 'store'])
            ->middleware('permission:admins.manage,admin')->name('admins.store');
        Route::get('/admins/{admin}', [AdminManagementController::class, 'show'])->name('admins.show');
        Route::get('/admins/{admin}/edit', [AdminManagementController::class, 'edit'])
            ->middleware('permission:admins.manage,admin')->name('admins.edit');
        Route::put('/admins/{admin}', [AdminManagementController::class, 'update'])
            ->middleware('permission:admins.manage,admin')->name('admins.update');
        Route::delete('/admins/{admin}', [AdminManagementController::class, 'destroy'])
            ->middleware('permission:admins.manage,admin')->name('admins.destroy');
        Route::get('/admins/{admin}/logs', [AdminManagementController::class, 'logs'])
            ->middleware('permission:activity-logs.view,admin')->name('admins.logs');
    });


    Route::middleware('permission:admins.manage,admin')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::middleware('permission:activity-logs.view,admin')->group(function () {
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::middleware('permission:api-keys.view,admin')->group(function () {
        Route::get('/api-keys', [\App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('api-keys.index');
    });

    Route::middleware('permission:settings.manage,admin')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    });
});