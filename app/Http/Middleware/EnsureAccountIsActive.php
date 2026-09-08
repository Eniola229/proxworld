<?php

namespace App\Http\Middleware;

use App\Types\AccountStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status !== AccountStatus::ACTIVE) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();

            $message = $user->status === AccountStatus::SUSPENDED
                ? 'Your account has been suspended. Contact support for help.'
                : 'Your account is not active. Contact support for help.';

            return redirect()->route('login')->withErrors(['email' => $message]);
        }

        return $next($request);
    }
}
