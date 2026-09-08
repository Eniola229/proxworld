<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyHasAbility
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $apiKey = $request->attributes->get('api_key');

        // null abilities = full access (matches the admin UI's "no restrictions" default).
        if ($apiKey && $apiKey->abilities !== null && ! in_array($ability, $apiKey->abilities)) {
            return response()->json(['message' => "This API key does not have the '{$ability}' ability."], 403);
        }

        return $next($request);
    }
}
