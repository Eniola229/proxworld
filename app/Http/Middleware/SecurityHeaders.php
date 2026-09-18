<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies security headers to every response.
 *
 * Registered globally (both web and admin routes run through this middleware).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME-type sniffing.
        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        // Prevent the site from being embedded in frames on other origins.
        $response->headers->set(
            'X-Frame-Options',
            'SAMEORIGIN'
        );

        // Control how much referrer information is sent.
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        // X-XSS-Protection is obsolete in modern browsers.
        // Explicitly disable it because CSP provides the protection.
        $response->headers->set(
            'X-XSS-Protection',
            '0'
        );

        // Browser feature permissions.
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(self), usb=(), interest-cohort=()'
        );

        // Only add our CSP if another middleware/controller has not
        // already supplied one.
        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                $this->buildCsp()
            );
        }

        /*
         * HSTS
         *
         * Only send HSTS over HTTPS in production.
         *
         * max-age=31536000 = 1 year
         */
        if (
            $request->secure()
            && app()->environment('production')
        ) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }

    /**
     * Build the Content Security Policy.
     */
    protected function buildCsp(): string
    {
        $directives = [
            /*
             * Default:
             * Only allow resources from this site's own origin unless
             * another directive explicitly allows them.
             */
            "default-src 'self'",

            /*
             * JavaScript
             *
             * Google Analytics / Google Tag:
             * https://www.googletagmanager.com
             * https://www.google-analytics.com
             *
             * Other existing application dependencies are also retained.
             */
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'
                https://cdn.jsdelivr.net
                https://checkout.flutterwave.com
                https://cdnjs.cloudflare.com
                https://unpkg.com
                https://static.cloudflareinsights.com
                https://www.googletagmanager.com
                https://www.google-analytics.com
                https://analytics.tiktok.com",

            /*
             * CSS
             */
            "style-src 'self' 'unsafe-inline'
                https://cdn.jsdelivr.net
                https://fonts.googleapis.com
                https://fonts.bunny.net
                https://unpkg.com
                https://cdnjs.cloudflare.com",

            /*
             * Fonts
             */
            "font-src 'self'
                https://fonts.gstatic.com
                https://fonts.bunny.net
                https://cdnjs.cloudflare.com
                https://cdn.jsdelivr.net
                data:",

            /*
             * Images
             *
             * https: is retained because the application may load images
             * from external HTTPS sources.
             */
            "img-src 'self'
                data:
                https:
                blob:
                https://res.cloudinary.com
                https://www.googletagmanager.com
                https://www.google-analytics.com
                https://analytics.tiktok.com",

            /*
             * Audio/video/media
             */
            "media-src 'self'
                https://res.cloudinary.com
                blob:",

            /*
             * AJAX / fetch / XMLHttpRequest / beacon / WebSocket connections.
             *
             * Google Analytics requires Google endpoints here so the
             * browser can send analytics events.
             */
            "connect-src 'self'
                https://api.cloudinary.com
                https://*.cloudinary.com
                https://api.flutterwave.com
                https://developersandbox-api.flutterwave.com
                https://cloudflareinsights.com
                https://www.googletagmanager.com
                https://www.google-analytics.com
                https://analytics.google.com
                https://*.google-analytics.com
                https://analytics.tiktok.com",

            /*
             * Frames / iframes.
             *
             * Flutterwave checkout requires these domains.
             */
            "frame-src 'self'
                https://checkout.flutterwave.com
                https://developersandbox.flutterwave.com
                https://*.flutterwave.cloud
                https://www.googletagmanager.com",

            /*
             * Disable plugins such as Flash/PDF plugins.
             */
            "object-src 'none'",

            /*
             * Prevent <base> from changing the document's base URL.
             */
            "base-uri 'self'",

            /*
             * Restrict where forms can submit.
             */
            "form-action 'self'
                https://checkout.flutterwave.com
                https://developersandbox.flutterwave.com
                https://*.flutterwave.com
                https://*.flutterwave.cloud",

            /*
             * Prevent other websites from framing this application.
             */
            "frame-ancestors 'self'",
        ];

        /*
         * Collapse whitespace/newlines so the final HTTP header is a
         * single clean CSP string.
         */
        return implode(
            '; ',
            array_map(
                static fn (string $directive): string =>
                    preg_replace('/\s+/', ' ', trim($directive)),
                $directives
            )
        );
    }
}
