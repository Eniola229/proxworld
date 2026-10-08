<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Bachs (https://docs.bachs.io) — Bearer SECRET KEY.
 *   Top-ups -> hosted checkout session priced as a raw amount (no catalog products).
 * Nothing from the browser is trusted. The signed collection.succeeded webhook is the only thing that credits a wallet.
 */
class BachsService
{
    /** Currencies Bachs can price a checkout in. */
    public const SUPPORTED_CURRENCIES = ['USD', 'NGN', 'GHS', 'KES', 'MWK', 'RWF', 'TZS', 'UGX', 'XAF', 'XOF', 'ZMW'];

    protected string $baseUrl;
    protected string $secretKey;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.bachs.base_url', 'https://api.bachs.io'), '/');
        $this->secretKey = (string) config('services.bachs.secret_key');
        $this->webhookSecret = (string) config('services.bachs.webhook_secret');
    }

    public static function supports(string $currency): bool
    {
        return in_array(strtoupper($currency), self::SUPPORTED_CURRENCIES, true);
    }

    protected function client()
    {
        if ($this->secretKey === '') {
            throw new RuntimeException('Payments are not configured yet. Please contact support.');
        }

        return Http::withToken($this->secretKey)->baseUrl($this->baseUrl)->acceptJson()->timeout(25);
    }

    /** Bachs refuses redirect URLs on loopback/private hosts (localhost, 127.0.0.1, 192.168.x.x ...). */
    protected function isPublicUrl(?string $url): bool
    {
        $host = strtolower((string) parse_url((string) $url, PHP_URL_HOST));

        if ($host === '' || $host === 'localhost' || str_ends_with($host, '.test') || str_ends_with($host, '.local')) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return (bool) filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        return true;
    }

    /** Hosted checkout for a raw amount. Returns ['checkout_id' => ..., 'checkout_url' => ...]. */
    public function createCheckout(array $payload): array
    {
        $reference = $payload['reference'] ?? throw new RuntimeException('A reference is required.');
        $currency = strtoupper($payload['currency']);
        $amount = (float) $payload['amount'];
        $meta = $payload['meta'] ?? [];

        $decimals = app(CurrencyService::class)->decimals($currency);

        $customer = array_filter([
            'email' => $payload['customer']['email'],
            'name' => trim((string) ($payload['customer']['name'] ?? '')) ?: null,
        ], fn ($v) => ! is_null($v));

        $successUrl = $this->isPublicUrl($payload['success_url'] ?? null) ? $payload['success_url'] : null;
        $cancelUrl = $this->isPublicUrl($payload['cancel_url'] ?? null) ? $payload['cancel_url'] : null;

        $body = array_filter([
            'pricing' => [
                'currency' => $currency,
                'amount' => number_format($amount, $decimals, '.', ''),
            ],
            // Pin the customer to the wallet currency. Without this Bachs picks the currency from the
            // customer's location (e.g. NGN in Nigeria) and the webhook would not match the wallet currency.
            'billing_currency' => $currency,
            'customer' => $customer,
            'reference' => $reference,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => $meta ? array_map('strval', $meta) : null,
            'expires_in_minutes' => 30,
        ], fn ($v) => ! is_null($v));

        $response = $this->client()
            ->withHeaders(['Idempotency-Key' => $reference])
            ->post('/v1/checkout-sessions', $body);

        $url = $response->json('checkout_url') ?? $response->json('data.checkout_url');

        if (! $response->successful() || ! $url) {
            Log::error('Bachs createCheckout failed', ['reference' => $reference, 'status' => $response->status(), 'body' => $response->body()]);

            throw new RuntimeException(
                $response->json('detail')
                    ?: $response->json('message')
                    ?: $response->json('error.message')
                    ?: 'Could not start payment.'
            );
        }

        $this->remember($reference, $meta, $amount, $currency);

        return [
            'checkout_id' => $response->json('checkout_id') ?? $response->json('data.checkout_id'),
            'checkout_url' => $url,
        ];
    }

    public function hasWebhookSecret(): bool
    {
        return $this->webhookSecret !== '';
    }

    /**
     * Signature = HMAC-SHA256 hex of "{timestamp}.{raw_body}" with the endpoint signing secret.
     * V2 header: "t=<ts>,v1=<sig>[,v1=<sig>...]" (several v1 values during a secret rotation — any match is valid).
     */
    public function verifySignature(string $rawBody, ?string $timestamp, ?string $signature, ?string $signatureV2, int $tolerance = 300): bool
    {
        if ($this->webhookSecret === '') {
            return false;
        }

        $candidates = [];
        $ts = null;

        if ($signatureV2) {
            foreach (explode(',', $signatureV2) as $part) {
                [$k, $v] = array_pad(explode('=', trim($part), 2), 2, '');

                if ($k === 't') {
                    $ts = $v;
                } elseif ($k === 'v1' && $v !== '') {
                    $candidates[] = $v;
                }
            }
        }

        if (! $candidates && $signature) {
            $candidates[] = $signature;
            $ts = $timestamp;
        }

        if (! $candidates || ! ctype_digit((string) $ts)) {
            return false;
        }

        if (abs(time() - (int) $ts) > $tolerance) {
            return false;
        }

        $expected = hash_hmac('sha256', $ts.'.'.$rawBody, $this->webhookSecret);

        foreach ($candidates as $candidate) {
            if (hash_equals($expected, $candidate)) {
                return true;
            }
        }

        return false;
    }

    public function getExpected(string $reference): ?array
    {
        return Cache::get("bachs:expected:{$reference}");
    }

    public function getCachedMeta(string $reference): array
    {
        return Cache::get("bachs:meta:{$reference}", []);
    }

    protected function remember(string $reference, array $meta, float $amount, string $currency): void
    {
        Cache::put("bachs:expected:{$reference}", ['amount' => $amount, 'currency' => $currency], now()->addDays(2));

        if (! empty($meta)) {
            Cache::put("bachs:meta:{$reference}", $meta, now()->addDays(2));
        }
    }
}