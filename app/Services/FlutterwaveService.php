<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Flutterwave v3 (Bearer SECRET KEY):
 *   NGN top-ups     -> one-time (dynamic) virtual bank account
 *   non-NGN top-ups -> hosted checkout link
 *   payouts         -> banks, account lookup, transfers
 * Nothing from the browser/webhook is trusted until verifyByReference() re-checks it with Flutterwave.
 */
class FlutterwaveService
{
    protected string $baseUrl;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.flutterwave.base_url', 'https://api.flutterwave.com/v3'), '/');
        $this->secretKey = (string) config('services.flutterwave.secret_key');
    }

    protected function client()
    {
        if ($this->secretKey === '') {
            throw new RuntimeException('Payments are not configured yet. Please contact support.');
        }

        return Http::withToken($this->secretKey)->baseUrl($this->baseUrl)->acceptJson()->timeout(25);
    }

    public static function splitCustomerName(string $name): array
    {
        $parts = explode(' ', trim($name), 2);

        return ['first' => $parts[0] ?: 'Customer', 'last' => $parts[1] ?? 'User'];
    }

    // ─── Payouts / lookups (same signatures as before) ───

    public function getBanks(string $country = 'NG'): array
    {
        $cacheKey = "flutterwave:v3:banks:{$country}";

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $response = $this->client()->get("/banks/{$country}");

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

    public function resolveAccount(string $accountNumber, string $bankCode, string $currency = 'NGN'): array
    {
        $response = $this->client()->post('/accounts/resolve', [
            'account_number' => $accountNumber,
            'account_bank' => $bankCode,
        ]);

        if (! $response->successful() || $response->json('status') !== 'success') {
            Log::warning('Flutterwave resolveAccount failed', ['bank_code' => $bankCode, 'account_number' => $accountNumber, 'response' => $response->json()]);

            throw new RuntimeException($response->json('message') ?? 'Could not resolve account name. Check the account number and bank.');
        }

        $data = $response->json('data', []);

        if (empty($data['account_name'] ?? null)) {
            throw new RuntimeException('Could not verify this account number for the selected bank. Please double-check the details.');
        }

        return $data;
    }

    public function initiateTransfer(array $payload): array
    {
        $reference = $payload['reference'] ?? throw new RuntimeException('A reference is required.');

        $body = array_filter([
            'account_bank' => $payload['account_bank'],
            'account_number' => $payload['account_number'],
            'amount' => (float) $payload['amount'],
            'currency' => $payload['currency'] ?? 'NGN',
            'debit_currency' => $payload['currency'] ?? 'NGN',
            'narration' => $payload['narration'] ?? null,
            'reference' => $reference,
            'callback_url' => $payload['callback_url'] ?? null,
        ], fn ($v) => ! is_null($v));

        $response = $this->client()->post('/transfers', $body);

        $data = $response->json();

        if (! $response->successful() || ($data['status'] ?? null) !== 'success') {
            Log::error('Flutterwave initiateTransfer failed', ['payload' => $body, 'body' => $response->body()]);

            throw new RuntimeException($data['message'] ?? 'Could not initiate transfer.');
        }

        return $data['data'] ?? [];
    }

    public function getTransfer(string $transferId): ?array
    {
        $response = $this->client()->get("/transfers/{$transferId}");

        if (! $response->successful()) {
            Log::error('Flutterwave getTransfer failed', ['transfer_id' => $transferId, 'body' => $response->body()]);

            return null;
        }

        return $response->json('data');
    }

    // ─── Collections ───

    /** NGN one-time bank account. Returns account_number, account_bank_name, amount, account_expiration_datetime, note. */
    public function createVirtualAccount(array $payload): array
    {
        $reference = $payload['reference'] ?? throw new RuntimeException('A reference is required.');
        $meta = $payload['meta'] ?? [];
        $amount = (float) $payload['amount'];
        $name = self::splitCustomerName($payload['customer']['name']);

        $response = $this->client()->post('/virtual-account-numbers', [
            'email' => $payload['customer']['email'],
            'is_permanent' => false,
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => 'NGN',
            'firstname' => $name['first'],
            'lastname' => $name['last'],
            'narration' => $payload['narration'] ?? 'Wallet top-up',
        ]);

        if (! $response->successful() || $response->json('status') !== 'success') {
            Log::error('Flutterwave createVirtualAccount failed', ['reference' => $reference, 'body' => $response->body()]);

            throw new RuntimeException($response->json('message') ?: 'Could not generate a payment account.');
        }

        $data = $response->json('data', []);

        $this->remember($reference, $meta, $amount, 'NGN');

        return [
            'account_number' => $data['account_number'] ?? null,
            'account_bank_name' => $data['bank_name'] ?? null,
            'amount' => $data['amount'] ?? $amount,
            'account_expiration_datetime' => $data['expiry_date'] ?? null,
            'note' => $data['note'] ?? null,
        ];
    }

    /** Hosted checkout for non-NGN top-ups. Returns the URL to redirect the customer to. */
    public function createCheckoutLink(array $payload): string
    {
        $reference = $payload['reference'] ?? throw new RuntimeException('A reference is required.');
        $meta = $payload['meta'] ?? [];
        $currency = strtoupper($payload['currency']);
        $amount = (float) $payload['amount'];

        $response = $this->client()->post('/payments', [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => trim((string) $payload['redirect_url']),
            'customer' => [
                'email' => $payload['customer']['email'],
                'name' => $payload['customer']['name'],
            ],
            'customizations' => ['title' => config('app.name', 'ProxWorld').' wallet top-up'],
            'session_duration' => 30,
        ]);

        $link = $response->json('data.link');

        if (! $response->successful() || $response->json('status') !== 'success' || ! $link) {
            Log::error('Flutterwave createCheckoutLink failed', ['reference' => $reference, 'body' => $response->body()]);

            throw new RuntimeException($response->json('message') ?: 'Could not start payment.');
        }

        $this->remember($reference, $meta, $amount, $currency);

        return $link;
    }

    /** Asks Flutterwave directly if this tx_ref was paid. Returns a normalized charge or null. */
    public function verifyByReference(string $reference): ?array
    {
        $response = $this->client()->get('/transactions/verify_by_reference', ['tx_ref' => $reference]);

        if (! $response->successful() || $response->json('status') !== 'success') {
            Log::warning('Flutterwave verifyByReference: lookup failed', ['reference' => $reference, 'body' => $response->body()]);

            return null;
        }

        $data = $response->json('data') ?? [];

        if (strtolower((string) ($data['status'] ?? '')) !== 'successful' || ($data['tx_ref'] ?? null) !== $reference) {
            return null;
        }

        $currency = strtoupper((string) ($data['currency'] ?? ''));
        $amount = (float) ($data['amount'] ?? 0);

        if ($currency === '' || $amount <= 0) {
            return null;
        }

        $expected = Cache::get("flutterwave:v3:expected:{$reference}");

        if ($expected && ($expected['currency'] !== $currency || $amount + 0.01 < (float) $expected['amount'])) {
            Log::error('Flutterwave verifyByReference: amount/currency mismatch', [
                'reference' => $reference, 'expected' => $expected, 'got' => ['amount' => $amount, 'currency' => $currency],
            ]);

            return null;
        }

        return [
            'id' => (string) ($data['id'] ?? ''),
            'reference' => $reference,
            'status' => 'succeeded',
            'amount' => $amount,
            'currency' => $currency,
            'meta' => [],
            'raw' => $data,
        ];
    }

    public function getCachedMeta(string $reference): array
    {
        return Cache::get("flutterwave:v3:meta:{$reference}", []);
    }

    protected function remember(string $reference, array $meta, float $amount, string $currency): void
    {
        Cache::put("flutterwave:v3:expected:{$reference}", ['amount' => $amount, 'currency' => $currency], now()->addDays(2));

        if (! empty($meta)) {
            Cache::put("flutterwave:v3:meta:{$reference}", $meta, now()->addDays(2));
        }
    }
}