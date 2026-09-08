<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Flutterwave v4 (OAuth2 client-credentials) service.
 *
 * Key differences from v3 you should know before touching this file:
 * - No static secret key. We exchange client_id/client_secret for a short-lived
 *   (10 min) access token and cache it, refreshing a little early.
 * - Every request needs an X-Trace-Id (any unique string) and every POST needs
 *   an X-Idempotency-Key (a retry with the same key must not double-charge/pay).
 *   We default the idempotency key to the caller's `reference` where possible,
 *   since that's already required to be unique per attempt.
 * - v4 has no single "hosted checkout, let the customer pick a method" endpoint
 *   yet (Flutterwave's own docs: "v4 Checkout ... still in work, coming very
 *   soon"). initializeTopUpCharge() below uses the NG Pay-with-Bank-Account
 *   (Mono redirect) method, which is the closest thing to your old flow:
 *   customer is redirected away, then redirected back, then a webhook confirms.
 * - v4 does NOT reliably echo the `meta` you set at charge-creation time back
 *   through the webhook payload or GET /charges/{id} (confirmed against
 *   Flutterwave's own docs — their example creates a charge with a populated
 *   meta object and the resulting webhook payload still shows meta:{}). Don't
 *   trust $charge['meta'] for identity resolution. We cache the meta we sent,
 *   keyed by our own reference, the same way we already cache charge-id and
 *   customer-id below — read it back with getCachedMeta().
 */
class FlutterwaveService
{
    protected string $baseUrl;
    protected string $authUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.flutterwave.base_url'), '/');
        $this->authUrl = (string) config('services.flutterwave.auth_url');
        $this->clientId = (string) config('services.flutterwave.client_id');
        $this->clientSecret = (string) config('services.flutterwave.client_secret');
    }

    /**
     * Get a cached OAuth2 access token, fetching/refreshing as needed.
     * Tokens are valid for 10 minutes (expires_in: 600) — we cache for a bit
     * less than that so we never hand out an about-to-expire token.
     */
    protected function accessToken(): string
    {
        return Cache::remember('flutterwave:v4:access_token', now()->addSeconds(540), function () {
            $response = Http::asForm()->post($this->authUrl, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ]);

            if (! $response->successful() || ! $response->json('access_token')) {
                Log::error('Flutterwave OAuth token request failed', ['body' => $response->body()]);

                throw new RuntimeException('Could not authenticate with Flutterwave.');
            }

            return $response->json('access_token');
        });
    }

    /**
     * Base authenticated client. Every request gets a fresh X-Trace-Id.
     * Pass $idempotencyKey for POST/PUT requests that mutate state.
     */
    protected function client(?string $idempotencyKey = null)
    {
        $client = Http::withToken($this->accessToken())
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->withHeaders(['X-Trace-Id' => (string) Str::uuid()]);

        if ($idempotencyKey) {
            $client = $client->withHeaders(['X-Idempotency-Key' => $idempotencyKey]);
        }

        return $client;
    }

    /**
     * If a token turns out to be stale/rejected mid-flight, clear the cache
     * so the next call fetches a fresh one instead of looping on 401s.
     */
    protected function forgetToken(): void
    {
        Cache::forget('flutterwave:v4:access_token');
    }

    /**
     * List banks for a country. Cached for the same reason as before — this
     * barely changes and admins would otherwise hammer Flutterwave on every load.
     *
     * NOTE: the v4 "Retrieve banks" endpoint is GET /banks. Confirm the exact
     * filter param (country vs country_code) against developer.flutterwave.com/reference
     * for your account before relying on this in production — docs for this
     * one specific endpoint were inconsistent at the time this was written.
     */
    public function getBanks(string $country = 'NG'): array
    {
        $cacheKey = "flutterwave:v4:banks:{$country}";

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $response = $this->client()->get('/banks', ['country' => $country]);

        if (! $response->successful()) {
            Log::error('Flutterwave getBanks failed', ['status' => $response->status(), 'body' => $response->body()]);

            return [];
        }

        $banks = $response->json('data', []);

        if (! empty($banks)) {
            Cache::put($cacheKey, $banks, now()->addDay());
        }

        return $banks;
    }

    /** Resolve account_number + bank code to the account holder's name. */
    public function resolveAccount(string $accountNumber, string $bankCode, string $currency = 'NGN'): array
    {
        $response = $this->client()->post('/banks/account-resolve', [
            'account' => [
                'code' => $bankCode,
                'number' => $accountNumber,
            ],
            'currency' => $currency,
        ]);

        if (! $response->successful() || $response->json('status') !== 'success') {
            Log::warning('Flutterwave resolveAccount failed', [
                'bank_code' => $bankCode,
                'account_number' => $accountNumber,
                'response' => $response->json(),
            ]);

            throw new RuntimeException($response->json('message') ?? 'Could not resolve account name. Check the account number and bank.');
        }

        $data = $response->json('data', []);

        if (empty($data['account_name'] ?? null)) {
            throw new RuntimeException('Could not verify this account number for the selected bank. Please double-check the details.');
        }

        return $data;
    }

    /**
     * Initialize a wallet top-up via the Orchestrator (one call creates the
     * customer, payment method, and charge). Uses NG Pay-with-Bank-Account:
     * the customer is redirected to a Mono-hosted page to pick their bank and
     * authorize, then redirected back to $payload['redirect_url'].
     *
     * $payload must include: amount, currency, reference, redirect_url,
     * customer (['email' => ..., 'name' => ['first' => ..., 'last' => ...]]).
     *
     * We cache reference => charge id so verifyTopUp() can look the charge up
     * reliably later without depending on undocumented redirect query params.
     * We also cache reference => meta (see class docblock) since v4 won't
     * hand the meta back to us later.
     */

    /**
     * Splits full name string into structured first/last array for v4 payload
     */
    public static function splitCustomerName(string $name): array
    {
        $parts = explode(' ', trim($name), 2);

        return [
            'first' => $parts[0] ?? 'Customer',
            'last'  => $parts[1] ?? 'User',
        ];
    }

    public function initializeTopUpCharge(array $payload): array
    {
        $reference = $payload['reference'] ?? throw new RuntimeException('A reference is required.');
        $meta = $payload['meta'] ?? [];

        $body = [
            'amount'         => (float) $payload['amount'],
            'currency'       => strtoupper($payload['currency']),
            'reference'      => $reference,
            'redirect_url'   => trim((string) $payload['redirect_url']),
            'payment_method' => [
                'type'         => 'bank_account',
                'bank_account' => (object) [],
            ],
            'customer'       => [
                'email' => $payload['customer']['email'],
                'name'  => self::splitCustomerName($payload['customer']['name']),
            ],
            'meta'           => (object) $meta,
        ];

        $response = $this->client($reference)->post('/orchestration/direct-charges', $body);

        if (! $response->successful()) {
            Log::error('Flutterwave initializeTopUpCharge failed', [
                'sent_payload' => $body,
                'body'         => $response->body(),
            ]);

            throw new RuntimeException($response->json('message') ?? 'Could not start payment.');
        }

        $data = $response->json('data', []);

        if (! empty($data['id'])) {
            Cache::put("flutterwave:v4:charge-id:{$reference}", $data['id'], now()->addDay());
        }

        if (! empty($meta)) {
            Cache::put("flutterwave:v4:charge-meta:{$reference}", $meta, now()->addDay());
        }

        return $data;
    }

    /** Fetch a charge by its Flutterwave id (chg_xxx). */
    public function getCharge(string $chargeId): ?array
    {
        $response = $this->client()->get("/charges/{$chargeId}");

        if (! $response->successful()) {
            Log::error('Flutterwave getCharge failed', ['charge_id' => $chargeId, 'body' => $response->body()]);

            return null;
        }

        return $response->json('data');
    }

    /** Fetch a charge by the reference we generated at initiation time. */
    public function getChargeByReference(string $reference): ?array
    {
        $chargeId = Cache::get("flutterwave:v4:charge-id:{$reference}");

        if (! $chargeId) {
            Log::warning("No cached Flutterwave charge id for reference {$reference}.");

            return null;
        }

        return $this->getCharge($chargeId);
    }

    /**
     * Retrieve the meta we cached at charge-initiation time, keyed by our own
     * reference. v4 doesn't reliably return this via the webhook or
     * GET /charges/{id} — see the class docblock. Returns [] if nothing was
     * cached (e.g. the charge predates this fix, or no meta was ever passed).
     */
    public function getCachedMeta(string $reference): array
    {
        return Cache::get("flutterwave:v4:charge-meta:{$reference}", []);
    }

    /**
     * Initiate a payout transfer via /direct-transfers — this is what actually
     * sends money for bank withdrawals. `reference` must be unique per attempt;
     * we also pass it as the idempotency key so a retry never double-pays.
     *
     * $payload: account_bank (bank CODE), account_number, amount, currency,
     * narration, reference, callback_url (optional).
     */
    public function initiateTransfer(array $payload): array
    {
        $reference = $payload['reference'] ?? throw new RuntimeException('A reference is required.');

        $body = [
            'action' => 'instant',
            'type' => 'bank',
            'reference' => $reference,
            'narration' => $payload['narration'] ?? null,
            'callback_url' => $payload['callback_url'] ?? null,
            'payment_instruction' => [
                'source_currency' => $payload['currency'],
                'destination_currency' => $payload['currency'],
                'amount' => [
                    'applies_to' => 'destination_currency',
                    'value' => $payload['amount'],
                ],
                'recipient' => [
                    'bank' => [
                        'account_number' => $payload['account_number'],
                        'code' => $payload['account_bank'],
                    ],
                ],
            ],
        ];

        $response = $this->client($reference)->post('/direct-transfers', array_filter($body, fn ($v) => ! is_null($v)));
        $data = $response->json();

        if (! $response->successful()) {
            Log::error('Flutterwave initiateTransfer failed', ['payload' => $body, 'body' => $response->body()]);

            throw new RuntimeException($data['message'] ?? 'Could not initiate transfer.');
        }

        return $data['data'] ?? [];
    }

    /** Fetch a transfer by its Flutterwave id (tfr_xxx). */
    public function getTransfer(string $transferId): ?array
    {
        $response = $this->client()->get("/transfers/{$transferId}");

        if (! $response->successful()) {
            Log::error('Flutterwave getTransfer failed', ['transfer_id' => $transferId, 'body' => $response->body()]);

            return null;
        }

        return $response->json('data');
    }

    /**
     * Create a one-time (dynamic) virtual account for a wallet top-up via PWBT.
     * Unlike the orchestrator flow, PWBT requires two calls: create the
     * customer first, then create the virtual account tied to that customer.
     *
     * $payload must include: amount, currency, reference, customer
     * (['email' => ..., 'name' => ['first' => ..., 'last' => ...]]),
     * and optionally expiry (seconds, default 900 = 15 mins for a top-up;
     * Flutterwave's own default is 3600, max is 31536000).
     */
    public function createVirtualAccount(array $payload): array
    {
        $reference = $payload['reference'] ?? throw new RuntimeException('A reference is required.');
        $meta = $payload['meta'] ?? [];

        $customerId = $this->resolveCustomerId(
            $payload['customer']['email'],
            $payload['customer']['name'],
            $reference
        );

        $body = [
            'reference'    => $reference,
            'customer_id'  => $customerId,
            'amount'       => (float) $payload['amount'],
            'currency'     => strtoupper($payload['currency']),
            'bank_code'    => $payload['bank_code'] ?? config('services.flutterwave.va_bank_code', '090567'),
            'account_type' => 'dynamic',
            'expiry'       => $payload['expiry'] ?? 900,
            'narration'    => $payload['narration'] ?? 'Wallet top-up',
            'meta'         => (object) $meta,
        ];

        $vaResponse = $this->client($reference)->post('/virtual-accounts', $body);

        if (! $vaResponse->successful()) {
            Log::error('Flutterwave createVirtualAccount failed', ['sent_payload' => $body, 'body' => $vaResponse->body()]);
            throw new RuntimeException($vaResponse->json('message') ?? 'Could not generate a payment account.');
        }

        $data = $vaResponse->json('data', []);

        if (! empty($data['id'])) {
            Cache::put("flutterwave:v4:va-id:{$reference}", $data['id'], now()->addDay());
        }

        if (! empty($meta)) {
            Cache::put("flutterwave:v4:charge-meta:{$reference}", $meta, now()->addDay());
        }

        return $data;
    }

    /**
     * Get an existing Flutterwave customer_id for this email, creating one only
     * if it doesn't already exist. Flutterwave enforces one customer per email
     * (RESOURCE_CONFLICT / 10409 on duplicate create), so every repeat top-up
     * attempt from the same user must reuse the same customer_id rather than
     * trying to create a fresh one each time.
     */
    protected function resolveCustomerId(string $email, string $name, string $idempotencyKey): string
    {
        // Cache the mapping so we don't hit /customers/search on every single
        // top-up once we've resolved it for this email at least once.
        $cacheKey = 'flutterwave:v4:customer-id:' . md5(strtolower($email));

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $createResponse = $this->client($idempotencyKey)->post('/customers', [
            'email' => $email,
            'name'  => self::splitCustomerName($name),
        ]);

        if ($createResponse->successful()) {
            $customerId = $createResponse->json('data.id');
            Cache::put($cacheKey, $customerId, now()->addYear());

            return $customerId;
        }

        // Customer already exists for this email — look it up instead of failing.
        if ($createResponse->json('error.code') === '10409') {
            $searchResponse = $this->client()->post('/customers/search', ['email' => $email]);

            if ($searchResponse->successful()) {
                $customerId = $searchResponse->json('data.0.id');

                if ($customerId) {
                    Cache::put($cacheKey, $customerId, now()->addYear());

                    return $customerId;
                }
            }

            Log::error('Flutterwave customer search failed after 409 conflict', ['email' => $email, 'body' => $searchResponse->body()]);
        } else {
            Log::error('Flutterwave create customer (PWBT) failed', ['body' => $createResponse->body()]);
        }

        throw new RuntimeException('Could not set up payment customer.');
    }

}