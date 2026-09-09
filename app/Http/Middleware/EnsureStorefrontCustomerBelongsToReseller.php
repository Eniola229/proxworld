<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards authenticated storefront routes (account pages, order history,
 * etc.) against a customer who's logged in but belongs to a DIFFERENT
 * reseller than the one whose subdomain they're currently on. Run this
 * AFTER 'storefront' (ResolveResellerFromSubdomain) and 'auth:web' in the
 * middleware stack — it depends on both having already run.
 */
class EnsureStorefrontCustomerBelongsToReseller
{
    public function handle(Request $request, Closure $next): Response
    {
        $reseller = $request->attributes->get('storefront_reseller');
        $user = Auth::guard('web')->user();

        if ($reseller && $user && $user->reseller_id !== $reseller->id) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('storefront.login')->with('alert', [
                'type' => 'error',
                'message' => 'Your account is not registered on this panel.',
            ]);
        }

        return $next($request);
    }
}