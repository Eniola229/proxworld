<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin panel — separate route file, separate cookie, separate guard.
            // Prefix is env-driven (ADMIN_ROUTE_PREFIX) via config/proxworld.php
            // so the admin login URL doesn't have to stay on the guessable
            // "control-panel" default — change the env var, no code changes.
            \Illuminate\Support\Facades\Route::middleware(['admin.session', 'web'])
                ->prefix(config('proxworld.admin_route_prefix', 'control-panel'))
                ->as('admin.')
                ->group(base_path('routes/admin.php'));

            // Reseller panel (dashboard, orders, wallet, etc.) — same `users`
            // table/guard as customers, just an is_reseller flag + its own
            // route namespace/prefix.
            \Illuminate\Support\Facades\Route::middleware(['web'])
                ->prefix('reseller')
                ->as('reseller.')
                ->group(base_path('routes/reseller.php'));

            // White-label storefront (a reseller's own end customers) — its
            // own auth controller/views, no path prefix or name prefix: it
            // lives at the subdomain root, not under /reseller.
            \Illuminate\Support\Facades\Route::middleware(['web'])
                ->group(base_path('routes/storefront.php'));

            // Public "pay for API" surface — token auth via Sanctum.
            \Illuminate\Support\Facades\Route::prefix('api/v1')
                ->as('api.')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->trustProxies(at: '*');

    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            // Runs BEFORE the `web` group on admin routes so the session
            // cookie name is swapped out before StartSession boots the
            // session — this is what keeps an admin session and a customer
            // session from ever colliding, even in the same browser.
            'admin.session' => \App\Http\Middleware\SetAdminSessionCookie::class,
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
            'admin.guest' => \App\Http\Middleware\RedirectIfAdminAuthenticated::class,
            'reseller' => \App\Http\Middleware\EnsureUserIsReseller::class,
            'account.active' => \App\Http\Middleware\EnsureAccountIsActive::class,
            'sufficient.balance' => \App\Http\Middleware\EnsureBalanceIsPositive::class,
            'api_key' => \App\Http\Middleware\AuthenticateApiKey::class,
            'api_key.ability' => \App\Http\Middleware\EnsureApiKeyHasAbility::class,
            'storefront' => \App\Http\Middleware\ResolveResellerFromSubdomain::class,
            'storefront.customer' => \App\Http\Middleware\EnsureStorefrontCustomerBelongsToReseller::class,

            // Spatie permission middleware aliases (role/permission checks work
            // per-guard automatically — an admin's roles never leak to `web`).
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Logs every state-changing request (who did what) across both
        // guards — appended to `web` so it covers customer AND admin routes
        // (admin routes run `web` too, right after admin.session — see routing above).
        $middleware->appendToGroup('web', \App\Http\Middleware\LogActivity::class);

        // Flutterwave's webhook is server-to-server — it can't send a CSRF
        // token, and it's verified by signature header instead (see
        // FlutterwaveController::webhook).
        $middleware->validateCsrfTokens(except: [
            'wallet/flutterwave-webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
