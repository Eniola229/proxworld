<?php

namespace App\Http\Middleware;

use App\Models\TrackingHit;
use App\Models\TrackingSession;
use App\Support\UserAgentParser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    public const COOKIE = 'pw_vid';
    private const COOKIE_MINUTES = 60 * 24 * 730; // 2 years

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldTrack($request)) {
            return $next($request);
        }

        $visitor = null;

        try {
            $visitor = $this->captureVisit($request);
        } catch (\Throwable $e) {
            report($e); // tracking must never break the site
        }

        $response = $next($request);

        try {
            $this->linkUser($request, $visitor);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }

    private function shouldTrack(Request $request): bool
    {
        if (! in_array($request->method(), ['GET', 'POST'], true)) {
            return false;
        }

        $adminPrefix = trim((string) config('proxworld.admin_route_prefix', 'control-panel'), '/');

        if ($request->is(
            $adminPrefix, $adminPrefix . '/*', 'api/*', 'up', 'sitemap.xml', 'geo-debug',
            'webhooks/*', 'wallet/*-webhook', 'reseller/*', 'reset-password/*', 'email/verify/*'
        )) {
            return false;
        }

        // Only the main app host (reseller storefront domains are skipped)
        $appHost = strtolower(config('proxworld.base_domain') ?: (parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost'));
        $host = strtolower($request->getHost());

        if (! in_array($host, [$appHost, 'www.' . $appHost, 'localhost', '127.0.0.1'], true)) {
            return false;
        }

        return ! UserAgentParser::isBot((string) $request->userAgent());
    }

    /* ------------------------------------------------------------------
     | Recording
     * ------------------------------------------------------------------ */

    private function captureVisit(Request $request): ?TrackingSession
    {
        $token = (string) $request->cookie(self::COOKIE, '');
        $session = Str::isUuid($token)
            ? TrackingSession::where('visitor_token', $token)->first()
            : null;

        $isLanding = $request->isMethod('GET') && ! $request->ajax() && ! $request->expectsJson();

        if (! $isLanding) {
            return $session;
        }

        $source = $this->clean($request->query('utm_source'), 120, true);

        // Returning visitor with no UTM: nothing new to record.
        if ($source === null && $session) {
            return $session;
        }

        $source ??= 'direct';
        $utm = [
            'medium'   => $this->clean($request->query('utm_medium'), 120, true),
            'campaign' => $this->clean($request->query('utm_campaign'), 120, true),
            'content'  => $this->clean($request->query('utm_content'), 150),
            'term'     => $this->clean($request->query('utm_term'), 150),
        ];

        if (! $session) {
            return $this->createSession($request, $source, $utm);
        }

        // Ignore page refreshes of the same link within 30 seconds.
        $duplicate = TrackingHit::where('tracking_session_id', $session->id)
            ->where('source', $source)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->exists();

        if (! $duplicate) {
            $this->recordHit($request, $session, $source, $utm);
        }

        return $session;
    }

    private function createSession(Request $request, string $source, array $utm, $userId = null): TrackingSession
    {
        $session = TrackingSession::create($this->snapshot($request) + [
            'visitor_token'  => (string) Str::uuid(),
            'user_id'        => $userId,
            'linked_at'      => $userId ? now() : null,
            'first_source'   => $source,
            'first_medium'   => $utm['medium'] ?? null,
            'first_campaign' => $utm['campaign'] ?? null,
            'hits_count'     => 0,
        ]);

        Cookie::queue(Cookie::make(self::COOKIE, $session->visitor_token, self::COOKIE_MINUTES));

        $this->recordHit($request, $session, $source, $utm);

        return $session;
    }

    private function recordHit(Request $request, TrackingSession $session, string $source, array $utm): void
    {
        // Keep only utm_* params so tokens/codes in other query strings are never stored.
        $utmQuery = collect($request->query())
            ->filter(fn ($v, $k) => is_string($k) && str_starts_with($k, 'utm_') && is_string($v))
            ->all();

        $url = $request->url() . ($utmQuery ? '?' . http_build_query($utmQuery) : '');
        $referrer = $request->headers->get('referer');

        TrackingHit::create($this->snapshot($request) + [
            'tracking_session_id' => $session->id,
            'source'              => $source,
            'medium'              => $utm['medium'] ?? null,
            'campaign'            => $utm['campaign'] ?? null,
            'content'             => $utm['content'] ?? null,
            'term'                => $utm['term'] ?? null,
            'landing_url'         => mb_substr($url, 0, 2000),
            'referrer'            => $referrer ? mb_substr(strtok($referrer, '?'), 0, 1000) : null,
            'laravel_session_id'  => $request->hasSession() ? $request->session()->getId() : null,
            'created_at'          => now(),
        ]);

        $session->increment('hits_count', 1, ['last_hit_at' => now()]);
    }

    /** IP, country and device fields shared by sessions and hits. */
    private function snapshot(Request $request): array
    {
        [$code, $name] = $this->country($request);
        $ua = (string) $request->userAgent();

        return [
            'ip_address'   => $this->ip($request),
            'country_code' => $code,
            'country_name' => $name,
            'user_agent'   => mb_substr($ua, 0, 1000),
        ] + UserAgentParser::parse($ua);
    }

    private function ip(Request $request): ?string
    {
        $ip = $request->ip();

        if (config('services.geo.trust_cloudflare') && $request->header('CF-Connecting-IP')) {
            $ip = trim((string) $request->header('CF-Connecting-IP'));
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    }

    private function country(Request $request): array
    {

        $fake = strtoupper((string) config('services.geo.fake_country'));

        if (app()->isLocal() && preg_match('/^[A-Z]{2}$/', $fake)) {
            $name = class_exists(\Locale::class) ? \Locale::getDisplayRegion('-' . $fake, 'en') : null;

            return [$fake, $name ?: $fake];
        }

        if (! config('services.geo.trust_cloudflare')) {
            return [null, null];
        }

        $code = strtoupper(trim((string) $request->header('CF-IPCountry')));

        if (! preg_match('/^[A-Z]{2}$/', $code) || in_array($code, ['XX', 'T1'], true)) {
            return [null, null];
        }

        $name = class_exists(\Locale::class) ? \Locale::getDisplayRegion('-' . $code, 'en') : null;

        return [$code, $name ?: $code];
    }

    private function clean(mixed $value, int $max, bool $lower = false): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim(strip_tags($value));
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', '', $value) ?? '';

        if ($lower) {
            $value = mb_strtolower($value);
        }

        $value = mb_substr($value, 0, $max);

        return $value === '' ? null : $value;
    }

    /* ------------------------------------------------------------------
     | Linking a visitor to a user (register, login, Google)
     * ------------------------------------------------------------------ */

    private function linkUser(Request $request, ?TrackingSession $current): void
    {
        if (! $request->hasSession()) {
            return;
        }

        $user = Auth::guard('web')->user();

        if (! $user) {
            return;
        }

        $userId = $user->getAuthIdentifier();
        $key = (string) $userId;

        if ($request->session()->get('tracking_linked') === $key) {
            return;
        }

        // The user's ORIGINAL (first) tracking session.
        $primary = TrackingSession::where('user_id', $userId)->orderBy('created_at')->orderBy('id')->first();

        if ($current && (string) $current->user_id === $key) {
            // This browser already belongs to the user.
        } elseif ($primary) {
            // Returning user on a new browser/session: fold it into the original one.
            if ($current && $current->user_id === null) {
                $this->merge($current, $primary);
            }

            Cookie::queue(Cookie::make(self::COOKIE, $primary->visitor_token, self::COOKIE_MINUTES));
        } elseif ($current && $current->user_id === null) {
            // First time we see this user: this session becomes their first session.
            $current->forceFill(['user_id' => $userId, 'linked_at' => now()])->save();
        } else {
            // No usable cookie (or browser belonged to another user).
            $this->createSession($request, 'direct', [], $userId);
        }

        $request->session()->put('tracking_linked', $key);
    }

    private function merge(TrackingSession $from, TrackingSession $into): void
    {
        DB::transaction(function () use ($from, $into) {
            $moved = TrackingHit::where('tracking_session_id', $from->id)
                ->update(['tracking_session_id' => $into->id]);

            $into->increment('hits_count', $moved, ['last_hit_at' => now()]);
            $from->delete();
        });
    }
}