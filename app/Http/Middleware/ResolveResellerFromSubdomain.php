<?php

namespace App\Http\Middleware;

use App\Models\Reseller;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

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

        // Route group is now Route::domain('{domain}'), so the parameter
        // for URL generation is 'domain', not 'subdomain'. Reusing the
        // actual request host is correct for BOTH subdomain and custom
        // domain resellers — it just echoes back whatever host the
        // customer is currently on.
        URL::defaults(['domain' => $host]);

        return $next($request);
    }
}