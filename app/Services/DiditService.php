<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin wrapper around Didit's v3 API + webhook signature checks.
 * Docs: https://docs.didit.me
 */
class DiditService
{
    private function http(): PendingRequest
    {
        return Http::withHeaders(['x-api-key' => (string) config('services.didit.api_key')])
            ->acceptJson()
            ->timeout(15);
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.didit.base_url'), '/') . $path;
    }

    /**
     * Create a hosted verification session. Returns Didit's response
     * (session_id, url, status ...). Throws on failure.
     */
    public function createSession(string $vendorData, string $callbackUrl): array
    {
        $response = $this->http()->post($this->url('/v3/session/'), [
            'workflow_id'     => config('services.didit.workflow_id'),
            'vendor_data'     => $vendorData,      // our users.id — comes back in the webhook
            'callback'        => $callbackUrl,     // where Didit sends the user afterwards
            'callback_method' => 'both',
        ]);

        if ($response->failed()) {
            Log::error('Didit create session failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new RuntimeException('Could not start verification.');
        }

        $data = $response->json();

        if (empty($data['url']) || empty($data['session_id'])) {
            Log::error('Didit create session: unexpected response', ['body' => $response->body()]);
            throw new RuntimeException('Could not start verification.');
        }

        return $data;
    }

    /** Ask Didit for the current status of a session ("Approved", "Declined", ...). */
    public function fetchStatus(string $sessionId): ?string
    {
        $response = $this->http()->get($this->url('/v3/session/' . rawurlencode($sessionId) . '/decision/'));

        if ($response->failed()) {
            Log::warning('Didit fetch decision failed', [
                'session_id' => $sessionId,
                'status'     => $response->status(),
            ]);
            return null;
        }

        return $response->json('status');
    }

    /**
     * Verify a webhook. Returns which signature matched ('v2' | 'raw' | 'simple')
     * or null when the request should be rejected.
     */
    public function verifyWebhook(Request $request): ?string
    {
        $secret = (string) config('services.didit.webhook_secret');
        $timestamp = (string) $request->header('X-Timestamp');

        if ($secret === '' || $timestamp === '' || abs(time() - (int) $timestamp) > 300) {
            return null;
        }

        $raw = $request->getContent();

        $v2 = (string) $request->header('X-Signature-V2');
        if ($v2 !== '' && $this->matchesV2($raw, $v2, $secret)) {
            return 'v2';
        }

        $sig = (string) $request->header('X-Signature');
        if ($sig !== '' && hash_equals(hash_hmac('sha256', $raw, $secret), $sig)) {
            return 'raw';
        }

        // Envelope-only signature. The caller must NOT trust the body when this returns 'simple'.
        $simple = (string) $request->header('X-Signature-Simple');
        if ($simple !== '') {
            $body = json_decode($raw, true);
            if (is_array($body)) {
                $canonical = implode(':', [
                    $body['timestamp'] ?? '',
                    $body['session_id'] ?? '',
                    $body['status'] ?? '',
                    $body['webhook_type'] ?? '',
                ]);
                if (hash_equals(hash_hmac('sha256', $canonical, $secret), $simple)) {
                    return 'simple';
                }
            }
        }

        return null;
    }

    private function matchesV2(string $raw, string $signature, string $secret): bool
    {
        // Decode to objects (not arrays) so empty JSON objects stay {} when re-encoded.
        $decoded = json_decode($raw, false);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $canonical = json_encode(
            $this->canonicalize($decoded),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return is_string($canonical) && hash_equals(hash_hmac('sha256', $canonical, $secret), $signature);
    }

    private function canonicalize($value)
    {
        if ($value instanceof \stdClass) {
            $props = get_object_vars($value);
            ksort($props, SORT_STRING);
            $sorted = new \stdClass();
            foreach ($props as $key => $item) {
                $sorted->{$key} = $this->canonicalize($item);
            }
            return $sorted;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->canonicalize($item), $value);
        }

        return $value;
    }
}
