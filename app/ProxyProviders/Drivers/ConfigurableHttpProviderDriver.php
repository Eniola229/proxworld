<?php

namespace App\ProxyProviders\Drivers;

use App\Models\Provider;
use App\ProxyProviders\Contracts\ProxyProviderContract;
use App\ProxyProviders\Exceptions\ProviderOrderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * Config-driven provider driver — no new class/deploy needed for a new
 * supplier as long as its API is a plain REST JSON API. Everything about
 * the provider's shape is described in the `config` JSON on the Provider
 * row, filled in on the admin "Add Provider" form.
 *
 * Config schema:
 * {
 *   "auth": {
 *     "type": "bearer" | "header" | "query",
 *     "header_name": "X-Access-Token",   // required when type = header
 *     "query_param": "api_key"           // required when type = query
 *   },
 *   "endpoints": {
 *     "products": "/products",
 *     "balance": "/account/balance",
 *     "orders_create": "/orders",
 *     "order_status": "/orders/{id}",
 *     "order_proxies": "/orders/{id}/proxies",
 *     "order_extend": "/orders/{id}/extend"      // optional
 *   },
 *   "request_fields": {
 *     "product_id_key": "product_id",
 *     "quantity_key": "quantity"
 *   },
 *   "response_paths": {
 *     "products_list": "data",       // dot path to the array of products, "" = root
 *     "product_id": "id",
 *     "product_name": "name",
 *     "product_rate": "price",
 *     "product_currency": "currency",   // optional, falls back to top-level "currency"
 *     "balance": "balance",
 *     "order_id": "order_id",
 *     "order_status": "status",
 *     "proxies_list": "data"
 *   },
 *   "currency": "USD",
 *   "supports_extend": false
 * }
 */
class ConfigurableHttpProviderDriver implements ProxyProviderContract
{
    protected string $baseUrl;

    protected ?string $apiKey;

    protected array $config;

    public function __construct(protected Provider $provider)
    {
        $this->baseUrl = rtrim($provider->api_url, '/');
        $this->apiKey = $provider->api_key;
        $this->config = $provider->config ?? [];

        foreach (['auth', 'endpoints', 'response_paths'] as $required) {
            if (! isset($this->config[$required])) {
                throw new InvalidArgumentException(
                    "Provider [{$provider->name}] config is missing the required \"{$required}\" section."
                );
            }
        }
    }

    protected function client()
    {
        $authType = $this->config['auth']['type'] ?? 'bearer';

        $http = Http::baseUrl($this->baseUrl)->acceptJson()->timeout(20);

        return match ($authType) {
            'bearer' => $http->withToken($this->apiKey),
            'header' => $http->withHeaders([
                $this->config['auth']['header_name']
                    ?? throw new InvalidArgumentException('auth.header_name is required for auth.type "header".')
                    => $this->apiKey,
            ]),
            'query' => $http, // query key appended per-request in endpointUrl()
            default => throw new InvalidArgumentException("Unsupported auth.type \"{$authType}\"."),
        };
    }

    protected function endpoint(string $key): string
    {
        $path = $this->config['endpoints'][$key] ?? null;

        if (! $path) {
            throw new InvalidArgumentException("Provider config is missing endpoints.{$key}.");
        }

        return $path;
    }

    protected function withQueryAuth(array $query = []): array
    {
        if (($this->config['auth']['type'] ?? null) === 'query') {
            $param = $this->config['auth']['query_param']
                ?? throw new InvalidArgumentException('auth.query_param is required for auth.type "query".');
            $query[$param] = $this->apiKey;
        }

        return $query;
    }

    protected function path(string $key, $default = null)
    {
        $dotPath = $this->config['response_paths'][$key] ?? null;

        return $dotPath === null ? $default : $dotPath;
    }

    protected function extract(array $body, string $key, $default = null)
    {
        $dotPath = $this->path($key);

        if ($dotPath === null || $dotPath === '') {
            return $body[$key] ?? $default;
        }

        return data_get($body, $dotPath, $default);
    }

    public function getProducts(): array
    {
        $response = $this->client()->get($this->endpoint('products'), $this->withQueryAuth());

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch products: '.$response->body());
        }

        $body = $response->json() ?? [];
        $listPath = $this->path('products_list', '');
        $list = $listPath === '' ? $body : (data_get($body, $listPath) ?? []);

        return collect($list)->map(function ($product) {
            return [
                'external_service_id' => (string) data_get($product, $this->path('product_id', 'id')),
                'name' => data_get($product, $this->path('product_name', 'name'), 'Unnamed plan'),
                'type' => data_get($product, $this->path('product_type', 'type')),
                'unit' => data_get($product, $this->path('product_unit', 'unit'), 'GB'),
                'rate' => (float) data_get($product, $this->path('product_rate', 'price'), 0),
                'currency' => data_get($product, $this->path('product_currency'), $this->config['currency'] ?? 'USD'),
                'raw' => $product,
            ];
        })->all();
    }

    public function getBalance(): float
    {
        $response = $this->client()->get($this->endpoint('balance'), $this->withQueryAuth());

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch balance: '.$response->body());
        }

        return (float) $this->extract($response->json() ?? [], 'balance', 0);
    }

    public function getBalanceCurrency(): string
    {
        return $this->config['currency'] ?? 'USD';
    }

    public function placeOrder(array $params): array
    {
        $fields = $this->config['request_fields'] ?? [];
        $productKey = $fields['product_id_key'] ?? 'product_id';
        $quantityKey = $fields['quantity_key'] ?? 'quantity';

        $response = $this->client()->post($this->endpoint('orders_create'), $this->withQueryAuth([
            $productKey => $params['external_service_id'],
            $quantityKey => $params['quantity'],
        ]));

        if (! $response->successful()) {
            throw new ProviderOrderException(
                'Order placement failed: '.$response->body(),
                $response->json()
            );
        }

        $body = $response->json() ?? [];

        return [
            'api_order_id' => (string) $this->extract($body, 'order_id', $body['id'] ?? null),
            'raw' => $body,
        ];
    }

    public function getOrderStatus(string $apiOrderId): string
    {
        $url = str_replace('{id}', $apiOrderId, $this->endpoint('order_status'));
        $response = $this->client()->get($url, $this->withQueryAuth());

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch order status: '.$response->body());
        }

        return (string) $this->extract($response->json() ?? [], 'order_status', 'pending');
    }

    public function extendOrder(string $apiOrderId, array $params = []): array
    {
        if (empty($this->config['supports_extend'])) {
            throw new \RuntimeException('This provider does not support order extension via API.');
        }

        $url = str_replace('{id}', $apiOrderId, $this->endpoint('order_extend'));
        $response = $this->client()->post($url, $this->withQueryAuth($params));

        if (! $response->successful()) {
            throw new ProviderOrderException('Extend failed: '.$response->body());
        }

        return $response->json() ?? [];
    }

    public function listProxies(string $apiOrderId): array
    {
        $url = str_replace('{id}', $apiOrderId, $this->endpoint('order_proxies'));
        $response = $this->client()->get($url, $this->withQueryAuth());

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to list proxies: '.$response->body());
        }

        $body = $response->json() ?? [];
        $listPath = $this->path('proxies_list', '');

        return $listPath === '' ? $body : (data_get($body, $listPath) ?? []);
    }
}