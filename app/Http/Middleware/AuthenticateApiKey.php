<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates via the custom `api_keys` table (not Sanctum) — looked up
 * by a fast indexed hash, never by decrypting every stored key. Expects
 * `Authorization: Bearer pxw_xxxxx`. Sets $request->apiKeyUser() and
 * $request->apiKeyAbilities() for downstream controllers/middleware.
 */
class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainKey = $request->bearerToken();

        if (! $plainKey) {
            return response()->json(['message' => 'Missing API key.'], 401);
        }

        $apiKey = ApiKey::where('key_hash', ApiKey::hashKey($plainKey))
            ->where('status', 'active')
            ->first();

        if (! $apiKey) {
            return response()->json(['message' => 'Invalid or inactive API key.'], 401);
        }

        $apiKey->forceFill(['last_used_at' => now(), 'last_used_ip' => $request->ip()])->save();

        $request->attributes->set('api_key', $apiKey);
        $request->setUserResolver(fn () => $apiKey->user);

        return $next($request);
    }
}
