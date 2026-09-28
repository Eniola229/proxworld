<?php

namespace App\Http\Middleware;

use App\Types\KycStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API equivalent of EnsureKycVerified — same balance-then-KYC ordering,
 * but returns JSON instead of redirecting (there is no page to redirect to
 * over the API). Register as 'kyc.verified.api' in bootstrap/app.php and
 * apply it after whatever middleware checks wallet balance on the API's
 * order-creation route.
 */
class EnsureKycVerifiedApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->kyc_status !== KycStatus::VERIFIED) {
            return response()->json([
                'message' => 'Please verify your identity before placing an order. Go to your ProxWorld account, open Profile, and complete verification.',
                'error'   => 'kyc_unverified',
            ], 403);
        }

        return $next($request);
    }
}
