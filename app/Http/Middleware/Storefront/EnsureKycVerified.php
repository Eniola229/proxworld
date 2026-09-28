<?php

namespace App\Http\Middleware\Storefront;

use App\Types\KycStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reseller-storefront version of EnsureKycVerified — same rule (balance
 * check first, then this), just redirects to storefront.kyc.show instead
 * of the main site's kyc.show. Register as 'storefront.kyc.verified' in
 * bootstrap/app.php.
 */
class EnsureKycVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->kyc_status !== KycStatus::VERIFIED) {
            return redirect()->route('storefront.kyc.show')
                ->with('alert', ['type' => 'warning', 'message' => 'Please verify your identity before placing an order.']);
        }

        return $next($request);
    }
}
