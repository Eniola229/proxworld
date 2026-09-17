<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsReseller
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $reseller = $user?->reseller;

        if (! $user || ! $user->is_reseller || ! $reseller) {
            abort(403, 'This area is for approved resellers only.');
        }

        if ($reseller->status !== \App\Types\ResellerStatus::APPROVED) {
            return redirect()->route('reseller.pending');
        }

        return $next($request);
    }
}