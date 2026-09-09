<?php

namespace App\ProxyProviders\Drivers;

use App\Models\Provider;
use App\ProxyProviders\Contracts\ProxyProviderContract;
use App\ProxyProviders\Exceptions\ProviderOrderException;
use Illuminate\Support\Facades\Http;

/**
 * Template driver for a "custom header token" style provider (this shape
 * matches providers like IPRoyal that authenticate with their own header,
 * e.g. X-Access-Token or X-API-Key, rather than a standard Authorization
 * Bearer header).
 *
 * IMPORTANT: verify the exact header name, endpoint paths, and response
 * shape against the specific provider's current API docs before going
 * live — providers update their APIs independently of this codebase.
 */
class HeaderTokenProviderDriver implements ProxyProviderContract
{
    protected string $baseUrl;

    protected ?string $apiKey;

    /** Adjust to match the specific provider, e.g. 'X-Access-Token', 'X-API-Key'. */
    protected string $headerName = 'X-Access-Token';

    public function __construct(protected Provider $provider)
    {
        $this->baseUrl = rtrim($provider->api_url, '/');
        $this->apiKey = $provider->api_key;
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([$this->headerName => $this->apiKey])
            ->acceptJson()
            ->timeout(20);
    }

    public function getProducts(): array
    {
        $response = $this->client()->get('/products');

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch products: '.$response->body());
        }

        $products = [];

        foreach ($response->json('data', []) as $product) {
            foreach ($product['plans'] ?? [] as $plan) {
                $products[] = [
                    'external_service_id' => (string) $plan['id'],
                    'name' => $product['name'].' — '.$plan['name'],
                    'type' => strtolower($product['name']),
                    'unit' => 'GB',
                    'rate' => (float) $plan['price'],
                    'currency' => $plan['currency'] ?? $product['currency'] ?? 'USD',
                    'raw' => $plan,
                ];
            }
        }

        return $products;
    }
    
    public function getBalance(): float
    {
        $response = $this->client()->get('/balance');

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch balance: '.$response->body());
        }

        return (float) ($response->json('balance') ?? $response->json('data.balance') ?? 0);
    }

    public function getBalanceCurrency(): string
    {
        return 'USD';
    }

    public function placeOrder(array $params): array
    {
        $response = $this->client()->post('/orders', [
            'sku' => $params['external_service_id'],
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
        throw new \RuntimeException('This provider does not support order extension via API.');
    }

    public function listProxies(string $apiOrderId): array
    {
        $response = $this->client()->get("/orders/{$apiOrderId}/list");

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to list proxies: '.$response->body());
        }

        return $response->json('data', $response->json());
    }
}
