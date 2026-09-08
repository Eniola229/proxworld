<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Activitylog\Facades\Activity;

/**
 * Logs "who did what" for every state-changing request (POST/PUT/PATCH/
 * DELETE), across BOTH guards. Read-only GET requests are skipped to avoid
 * flooding the log with page views — that's not what this is for.
 *
 * Sits in the middleware stack for both the customer `web` group and the
 * admin route group, so causer_guard tells you which side of the app an
 * action came from without joining anything. Viewed + filtered in the
 * admin panel behind the `activity-logs.view` permission.
 */
class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }

        // Skip noisy/irrelevant routes (asset requests, health checks, the
        // ticket-chat polling endpoint itself would be GET anyway).
        if ($request->is('up') || $request->is('_debugbar/*')) {
            return $response;
        }

        $admin = Auth::guard('admin')->user();
        $user = Auth::guard('web')->user();
        $causer = $admin ?? $user;

        if (! $causer) {
            return $response;
        }

        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;

        Activity::causedBy($causer)
            ->withProperties([
                'input' => $this->redactSensitive($request->except(['password', 'password_confirmation', '_token'])),
                'status_code' => $status,
            ])
            ->tap(function ($activity) use ($request, $admin) {
                $activity->causer_guard = $admin ? 'admin' : 'web';
                $activity->ip_address = $request->ip();
                $activity->method = $request->method();
                $activity->url = $request->fullUrl();
                $activity->event = Str::of($request->route()?->getName() ?? $request->path())->replace('.', '_');
            })
            ->log($this->describe($request));

        return $response;
    }

    protected function describe(Request $request): string
    {
        $routeName = $request->route()?->getName();

        return $routeName ? "Route: {$routeName}" : "{$request->method()} {$request->path()}";
    }

    protected function redactSensitive(array $input): array
    {
        foreach (['api_key', 'secret', 'card_number', 'cvv'] as $key) {
            if (isset($input[$key])) {
                $input[$key] = '[REDACTED]';
            }
        }

        return $input;
    }
}
