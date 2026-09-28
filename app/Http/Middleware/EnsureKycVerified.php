<?php

namespace App\Http\Middleware;

use App\Types\KycStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs AFTER 'sufficient.balance' on the order.store route — only blocks the
 * order if the user could actually afford it. Register the alias in
 * bootstrap/app.php: 'kyc.verified' => \App\Http\Middleware\EnsureKycVerified::class
 *
 * Main-site version — redirects into the KYC page. See
 * EnsureKycVerifiedApi for the API's JSON version and
 * Storefront\EnsureKycVerified for the reseller-domain version.
 */
class EnsureKycVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->kyc_status !== KycStatus::VERIFIED) {
            return redirect()->route('kyc.show')
                ->with('error', 'Please verify your identity before placing an order.');
        }

        return $next($request);
    }
}
