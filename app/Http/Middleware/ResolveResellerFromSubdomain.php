<?php

namespace App\Http\Middleware;

use App\Models\Reseller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the Reseller behind a white-label storefront request, by
 * subdomain (e.g. acme.proxworld.test) or a verified custom_domain, and
 * shares it with every storefront view as $reseller. If nothing matches
 * an active reseller, 404s rather than falling through to the main site —
 * a mistyped/deactivated reseller subdomain shouldn't silently show the
 * main platform.
 */
class ResolveResellerFromSubdomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $appHost = config('proxworld.base_domain') ?: (parse_url(config('app.url'), PHP_URL_HOST) ?? $host);

        $reseller = Reseller::where('custom_domain', $host)->first();

        if (! $reseller && str_ends_with($host, ".{$appHost}")) {
            $subdomain = strtolower(substr($host, 0, -strlen(".{$appHost}")));
            $reseller = Reseller::where('subdomain', $subdomain)->first();
        }

        abort_unless($reseller && $reseller->isActive(), 404);

        View::share('reseller', $reseller);
        $request->attributes->set('storefront_reseller', $reseller);

        return $next($request);
    }
}
