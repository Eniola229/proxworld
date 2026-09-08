<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs before Illuminate\Session\Middleware\StartSession on the admin route
 * group. Swaps the session cookie name so an admin's session is physically
 * a different cookie from a customer's — meaning a customer and an admin
 * can be logged in simultaneously in the same browser without either
 * session bleeding into the other, and a stolen customer session cookie
 * has zero access to admin routes.
 */
class SetAdminSessionCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        config([
            'session.cookie' => env('ADMIN_SESSION_COOKIE', 'proxworld_admin_session'),
        ]);

        return $next($request);
    }
}
