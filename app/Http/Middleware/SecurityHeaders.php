<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies security headers to every response. Registered globally (both
 * `web` and admin routes run through it — see bootstrap/app.php). Values
 * are deliberately conservative defaults; tighten the CSP further once all
 * third-party asset domains (fonts, CDNs) actually in use are finalized.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '0'); // superseded by CSP; explicitly disabled per modern guidance
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(self), usb=(), interest-cohort=()'
        );

        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', $this->buildCsp());
        }

        // Only send HSTS over an actual HTTPS response in production — sending
        // it over plain HTTP (e.g. local dev) can lock a browser into HTTPS
        // for a domain that doesn't serve it yet.
        if ($request->secure() && app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }

    protected function buildCsp(): string
    {
        // Adjust the CDN/host lists here as you finalize which asset CDNs,
        // font hosts, and payment/embed domains the frontend actually uses.
        //
        // Flutterwave v4 redirects customers to different domains than v3's
        // checkout.flutterwave.com — sandbox charges (bank_account, card 3DS,
        // mobile money, etc.) land on developersandbox.flutterwave.com or a
        // *.flutterwave.cloud subdomain. Production will use flutterwave.com /
        // flutterwave.cloud equivalents once live keys are swapped in — check
        // the actual `Location` header on a live charge and tighten/adjust
        // this list then rather than leaving the wildcard in permanently.
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://checkout.flutterwave.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data:",
            "img-src 'self' data: https: blob:",
            "media-src 'self' https://res.cloudinary.com",
            "connect-src 'self' https://api.flutterwave.com https://developersandbox-api.flutterwave.com",
            "frame-src 'self' https://checkout.flutterwave.com https://developersandbox.flutterwave.com https://*.flutterwave.cloud",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self' https://checkout.flutterwave.com https://developersandbox.flutterwave.com https://*.flutterwave.com https://*.flutterwave.cloud",
            "frame-ancestors 'self'",
        ];

        return implode('; ', $directives);
    }
}
