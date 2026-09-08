<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cheap first-pass guard so a zero/negative-balance user never even reaches
 * the order form / API order endpoint. The *real*, race-condition-safe check
 * happens again inside WalletService::debit() with a row lock — this
 * middleware is just a fast, friendly early exit, not the security boundary.
 */
class EnsureBalanceIsPositive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (float) $user->balance <= 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Insufficient wallet balance. Please fund your wallet first.',
                ], 402);
            }

            return redirect()->route('wallet.index')->with('error', 'Please fund your wallet before placing an order.');
        }

        return $next($request);
    }
}
