<?php

namespace App\ProxyProviders\Drivers;

use App\Models\Provider;
use App\ProxyProviders\Contracts\ProxyProviderContract;
use App\ProxyProviders\Exceptions\ProviderOrderException;
use Illuminate\Support\Facades\Http;

/**
 * Template driver for a "Bearer token" style provider (this shape matches
 * how several major residential-proxy resellers, e.g. Smartproxy/Decodo,
 * structure their reseller/API access: Authorization: Bearer <key>,
 * JSON in/out, a /products or /plans catalog endpoint, and an /orders
 * endpoint for purchases).
 *
 * IMPORTANT: proxy providers change their exact endpoint paths and response
 * shapes over time and differ provider to provider. Before going live,
 * verify every endpoint below against the specific provider's current API
 * reference docs and adjust — this class is a correct-shaped starting
 * point, not a guarantee of an exact byte-for-byte match to any one
 * provider's live API today.
 */
class BearerTokenProviderDriver implements ProxyProviderContract
{
    protected string $baseUrl;

    protected ?string $apiKey;

    public function __construct(protected Provider $provider)
    {
        $this->baseUrl = rtrim($provider->api_url, '/');
        $this->apiKey = $provider->api_key;
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->apiKey)   // sends "Authorization: Bearer <key>" — the 'Token' override was wrong
            ->acceptJson()
            ->timeout(20);
    }

    public function getProducts(): array
    {
        $response = $this->client()->get('/products');

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch products: '.$response->body());
        }

        // Normalize to: [ ['id' => ..., 'name' => ..., 'type' => ..., 'rate' => ..., 'currency' => ..., 'unit' => ...], ... ]
        return collect($response->json('data', $response->json()))->map(fn ($p) => [
            'external_service_id' => (string) ($p['id'] ?? $p['product_id']),
            'name' => $p['name'] ?? $p['title'] ?? 'Unnamed plan',
            'type' => $p['type'] ?? null,
            'unit' => $p['unit'] ?? 'GB',
            'rate' => (float) ($p['price'] ?? $p['rate'] ?? 0),
            'currency' => $p['currency'] ?? 'USD',
            'raw' => $p,
        ])->all();
    }

    public function getBalance(): float
    {
        $response = $this->client()->get('/account/balance');

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch balance: '.$response->body());
        }

        return (float) ($response->json('balance') ?? 0);
    }

    public function getBalanceCurrency(): string
    {
        return 'USD';
    }

    public function placeOrder(array $params): array
    {
        $response = $this->client()->post('/orders', [
            'product_id' => $params['external_service_id'],
            'quantity' => $params['quantity'],
        ]);

        if (! $response->successful()) {
            throw new ProviderOrderException(
                'Order placement failed: '.$response->body(),
                $response->json()
            );
        }

        $data = $response->json();

        return [
            'api_order_id' => (string) ($data['order_id'] ?? $data['id']),
            'raw' => $data,
        ];
    }

    public function getOrderStatus(string $apiOrderId): string
    {
        $response = $this->client()->get("/orders/{$apiOrderId}");

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch order status: '.$response->body());
        }

        return (string) $response->json('status', 'pending');
    }

    public function extendOrder(string $apiOrderId, array $params = []): array
    {
        $response = $this->client()->post("/orders/{$apiOrderId}/extend", $params);

        if (! $response->successful()) {
            throw new ProviderOrderException('Extend failed: '.$response->body());
        }

        return $response->json();
    }

    public function listProxies(string $apiOrderId): array
    {
        $response = $this->client()->get("/orders/{$apiOrderId}/proxies");

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to list proxies: '.$response->body());
        }

        return $response->json('data', $response->json());
    }
}
