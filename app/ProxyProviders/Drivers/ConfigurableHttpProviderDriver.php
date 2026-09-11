<?php

namespace App\ProxyProviders\Drivers;

use App\Models\Provider;
use App\ProxyProviders\Contracts\ProxyProviderContract;
use App\ProxyProviders\Exceptions\ProviderOrderException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

/**
 * Config-driven provider driver — no new class/deploy needed for a new
 * supplier as long as its API is a plain REST JSON API. Everything about
 * the provider's shape is described in the `config` JSON on the Provider
 * row, filled in on the admin "Add Provider" form.
 *
 * (Schema docblock unchanged — see prior version for the full reference.)
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
            'dual_header' => $http->withHeaders([
                $this->config['auth']['public_header_name'] ?? 'X-API-Public-Key' => $this->apiKey,
                $this->config['auth']['private_header_name'] ?? 'X-API-Private-Key' => $this->provider->api_secret,
            ]),
            'query' => $http,
            default => throw new InvalidArgumentException("Unsupported auth.type \"{$authType}\"."),
        };
    }

    protected function endpoint(string $key): string
    {
        $path = $this->config['endpoints'][$key] ?? null;

        if (! $path) {
            throw new InvalidArgumentException("Provider [{$this->provider->name}] config is missing endpoints.{$key}.");
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
        return $this->config['response_paths'][$key] ?? $default;
    }

    /** Reads a value out of a decoded JSON body, tolerating a bare scalar body (no wrapper object). */
    protected function extract($body, string $key, $default = null)
    {
        if (! is_array($body)) {
            return $body;
        }

        $dotPath = $this->path($key);

        if ($dotPath === null || $dotPath === '') {
            return $body[$key] ?? $default;
        }

        return data_get($body, $dotPath, $default);
    }

    /**
     * Wraps data_get() so a missing/null config path always falls through to
     * $default, instead of Laravel's data_get() returning the ENTIRE $target
     * when the path is null (which silently produced arrays where scalars
     * were expected — the cause of the Byteful "Array to string conversion" bug).
     */
    protected function dig($target, ?string $dotPath, $default = null)
    {
        if ($dotPath === null || $dotPath === '') {
            return $default;
        }

        return data_get($target, $dotPath, $default);
    }

    protected function resolveOrderUrl(string $endpointKey, string $apiOrderId): array
    {
        $endpoint = $this->endpoint($endpointKey);
        $idParam = $this->config['request_fields']['order_id_query_param'] ?? null;

        if (str_contains($endpoint, '{id}')) {
            return [str_replace('{id}', $apiOrderId, $endpoint), []];
        }

        return [$endpoint, [($idParam ?? 'id') => $apiOrderId]];
    }

    protected function staticFields(): array
    {
        return $this->config['request_fields']['static_fields'] ?? [];
    }

    public function getProducts(): array
    {
        // No catalog endpoint configured (e.g. Residential/GB-based providers) —
        // nothing to sync, not an error. Seed that provider's one service manually.
        if (empty($this->config['endpoints']['products'])) {
            return [];
        }

        $response = $this->client()->get($this->endpoint('products'), $this->withQueryAuth());

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch products: '.$response->body());
        }

        $body = $response->json() ?? [];
        $listPath = $this->path('products_list', '');
        $list = $listPath === '' ? $body : (data_get($body, $listPath) ?? []);

        $shape = $this->config['products_shape'] ?? 'flat';

        $products = $shape === 'nested_plans_locations'
            ? $this->flattenNestedPlansAndLocations($list)
            : $this->flattenFlatProducts($list);

        if (! empty($this->config['products_filter'])) {
            $products = array_values(array_filter($products, function ($p) {
                foreach ($this->config['products_filter'] as $field => $expected) {
                    if (($p[$field] ?? null) !== $expected) {
                        return false;
                    }
                }

                return true;
            }));
        }

        if (($this->config['pricing']['source'] ?? 'response') === 'quote') {
            $products = array_map(fn ($p) => [...$p, 'rate' => $this->quotePriceFor($p['external_service_id'])], $products);
        }

        return $products;
    }

    protected function quotePriceFor(string $externalServiceId): float
    {
        $pricing = $this->config['pricing'] ?? [];
        $fields = $this->config['request_fields'] ?? [];

        $body = array_merge($this->staticFields(), [
            ($fields['product_id_key'] ?? 'product_id') => $externalServiceId,
            ($fields['quantity_key'] ?? 'quantity') => $pricing['quote_quantity'] ?? 1,
        ]);

        $response = $this->client()->post($this->endpoint('quote'), $this->withQueryAuth($body));

        if (! $response->successful()) {
            throw new ProviderOrderException("Failed to fetch quote for {$externalServiceId}: ".$response->body());
        }

        $rate = $this->dig($response->json() ?? [], $pricing['rate_path'] ?? null, 0);
        $divisor = $pricing['rate_divisor'] ?? 1;

        return $divisor > 0 ? ((float) $rate) / $divisor : (float) $rate;
    }

    protected function flattenFlatProducts(iterable $list): array
    {
        $keyedById = $this->config['products_keyed_by_id'] ?? false;
        $rows = [];

        foreach ($list as $key => $product) {
            $rows[] = [
                'external_service_id' => (string) ($keyedById ? $key : $this->dig($product, $this->path('product_id', 'id'))),
                'name' => $this->dig($product, $this->path('product_name', 'name'), 'Unnamed plan'),
                'type' => $this->config['product_type_override']
                    ?? $this->dig($product, $this->path('product_type', 'type')),
                'unit' => $this->dig($product, $this->path('product_unit', 'unit'), 'GB'),
                'rate' => (float) $this->dig($product, $this->path('product_rate', 'price'), 0),
                // FIXED: previously passed a null dot-path straight into data_get(),
                // which returns the whole $product array instead of the default.
                'currency' => $this->dig($product, $this->path('product_currency'), $this->config['currency'] ?? 'USD'),
                'raw' => $product,
            ];
        }

        return $rows;
    }

    protected function flattenNestedPlansAndLocations(iterable $list): array
    {
        $sep = $this->config['request_fields']['composite_id_separator'] ?? ':';
        $rows = [];

        foreach ($list as $product) {
            $productId = $this->dig($product, $this->path('product_id', 'id'));
            $productName = $this->dig($product, $this->path('product_name', 'name'));
            $plans = $this->dig($product, $this->path('product_plans', 'plans'), []);
            $locations = $this->dig($product, $this->path('product_locations', 'locations'), []);

            foreach ($plans as $plan) {
                $planId = $this->dig($plan, $this->path('plan_id', 'id'));
                $planName = $this->dig($plan, $this->path('plan_name', 'name'));
                $planRate = (float) $this->dig($plan, $this->path('plan_rate', 'price'), 0);

                $locationRows = count($locations) ? $locations : [null];

                foreach ($locationRows as $location) {
                    $locationId = $location ? $this->dig($location, $this->path('location_id', 'id')) : null;
                    $locationName = $location ? $this->dig($location, $this->path('location_name', 'name')) : null;

                    $idParts = array_filter([$productId, $planId, $locationId], fn ($v) => $v !== null);
                    $name = collect([$productName, $planName, $locationName])->filter()->implode(' — ');

                    $rows[] = [
                        'external_service_id' => implode($sep, $idParts),
                        'name' => $name ?: 'Unnamed plan',
                        'type' => $this->config['product_type_override'] ?? null,
                        'unit' => 'proxy',
                        'rate' => $planRate,
                        'currency' => $this->config['currency'] ?? 'USD',
                        'raw' => ['product' => $product, 'plan' => $plan, 'location' => $location],
                    ];
                }
            }
        }

        return $rows;
    }

    public function getBalance(): float
    {
        if (empty($this->config['endpoints']['balance'])) {
            return 0.0;
        }

        $response = $this->client()->get($this->endpoint('balance'), $this->withQueryAuth());

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch balance: '.$response->body());
        }

        $raw = (float) $this->extract($response->json(), 'balance', 0);
        $divisor = $this->config['balance_divisor'] ?? 1;

        return $divisor > 0 ? $raw / $divisor : $raw;
    }

    public function getBalanceCurrency(): string
    {
        return $this->config['currency'] ?? 'USD';
    }

    public function placeOrder(array $params): array
    {
        $fields = $this->config['request_fields'] ?? [];
        $body = array_merge($this->staticFields(), ['quantity' => $params['quantity']]);

        if (! empty($fields['composite_id_keys'])) {
            $sep = $fields['composite_id_separator'] ?? ':';
            $parts = explode($sep, $params['external_service_id']);

            foreach ($fields['composite_id_keys'] as $i => $key) {
                if (isset($parts[$i])) {
                    $body[$key] = is_numeric($parts[$i]) ? (int) $parts[$i] : $parts[$i];
                }
            }
        } else {
            $body[$fields['product_id_key'] ?? 'product_id'] = $params['external_service_id'];
        }

        $response = $this->client()->post($this->endpoint('orders_create'), $this->withQueryAuth($body));

        if (! $response->successful()) {
            throw new ProviderOrderException(
                'Order placement failed: '.$response->body(),
                $response->json()
            );
        }

        $data = $response->json() ?? [];

        $paidFlagPath = $this->path('paid_flag');
        if ($paidFlagPath && $this->dig($data, $paidFlagPath) === false) {
            throw new ProviderOrderException('Order created but unpaid — insufficient provider account credit.', $data);
        }

        return [
            'api_order_id' => (string) $this->extract($data, 'order_id', $data['id'] ?? null),
            'raw' => $data,
        ];
    }

    public function getOrderStatus(string $apiOrderId): string
    {
        if (empty($this->config['endpoints']['order_status'])) {
            return 'unknown';
        }

        [$url, $query] = $this->resolveOrderUrl('order_status', $apiOrderId);
        $response = $this->client()->get($url, $this->withQueryAuth($query));

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to fetch order status: '.$response->body());
        }

        return (string) $this->extract($response->json(), 'order_status', 'pending');
    }

    public function extendOrder(string $apiOrderId, array $params = []): array
    {
        if (empty($this->config['supports_extend'])) {
            throw new \RuntimeException('This provider does not support order extension via API.');
        }

        [$url, $query] = $this->resolveOrderUrl('order_extend', $apiOrderId);
        $response = $this->client()->post($url, $this->withQueryAuth(array_merge($query, $params)));

        if (! $response->successful()) {
            throw new ProviderOrderException('Extend failed: '.$response->body());
        }

        return $response->json() ?? [];
    }

    public function listProxies(string $apiOrderId): array
    {
        if (($this->config['proxies_per_order'] ?? true) === false) {
            throw new \RuntimeException($this->config['no_proxies_message'] ?? 'This provider has no per-order proxy credentials.');
        }

        [$url, $query] = $this->resolveOrderUrl('order_proxies', $apiOrderId);
        $response = $this->client()->get($url, $this->withQueryAuth($query));

        if (! $response->successful()) {
            throw new ProviderOrderException('Failed to list proxies: '.$response->body());
        }

        $body = $response->json() ?? [];
        $listPath = $this->path('proxies_list', '');

        return $listPath === '' ? $body : (data_get($body, $listPath) ?? []);
    }
}