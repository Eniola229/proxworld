<?php

namespace App\ProxyProviders\Contracts;

use App\Models\Provider;

/**
 * Implement this once per proxy supplier. Everything else in the app
 * (order processing job, catalog sync command, admin balance display)
 * talks only to this interface — never to a specific provider's SDK
 * directly — so adding a new supplier never touches existing code.
 */
interface ProxyProviderContract
{
    public function __construct(Provider $provider);

    /** Raw list of the provider's currently sellable plans/products. */
    public function getProducts(): array;

    /** Provider's current account balance, in whatever currency they report (see getBalanceCurrency()). */
    public function getBalance(): float;

    public function getBalanceCurrency(): string;

    /**
     * Places an order. $params typically includes external_service_id and
     * quantity. Must return ['api_order_id' => string, 'raw' => array].
     *
     * @throws \App\ProxyProviders\Exceptions\ProviderOrderException on failure
     */
    public function placeOrder(array $params): array;

    /** Returns a normalized status string your OrderStatus::* the caller maps against. */
    public function getOrderStatus(string $apiOrderId): string;

    /** Not all providers support this — implementations may throw \RuntimeException. */
    public function extendOrder(string $apiOrderId, array $params = []): array;

    /** Proxy credentials/list for a fulfilled order (IP:port:user:pass etc.), raw provider format. */
    public function listProxies(string $apiOrderId): array;
}
