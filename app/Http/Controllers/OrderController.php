<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProxyOrder;
use App\Models\Order;
use App\Models\Provider;
use App\Models\ProviderServiceCache;
use App\Services\ExchangeRateService;
use App\Services\PricingService;
use App\Services\WalletService;
use App\Support\CountryCodeResolver;
use App\Types\OrderChannel;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Http\Request;
use App\Services\CurrencyService;
use App\Models\OrderExtension;
use App\ProxyProviders\ProxyProviderFactory;
use App\Services\ProfitService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function create(CurrencyService $currencies)
    {
        $providersByType = Provider::active()
            ->withCount(['services as service_count' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->flatMap(function (Provider $provider) {
                return ProviderServiceCache::where('provider_id', $provider->id)
                    ->where('is_active', true)
                    ->distinct()
                    ->pluck('type')
                    ->map(fn ($type) => [
                        'type' => $type,
                        'provider_id' => $provider->id,
                        'provider_name' => $provider->name,
                    ]);
            })
            ->groupBy('type');

        return view('order.new', [
            'providersByType' => $providersByType,
            'currencyDisplay' => $currencies->display($currencies->walletCurrency(auth()->user())),
        ]);
    } 

    public function countries(Request $request)
    {
        $data = $request->validate([
            'provider_id' => ['required', 'uuid', 'exists:providers,id'],
            'type' => ['required', 'string'],
        ]);

        $names = ProviderServiceCache::where('provider_id', $data['provider_id'])
            ->where('type', $data['type'])
            ->where('is_active', true)
            ->pluck('name');

        $countries = $names
            ->map(fn ($name) => CountryCodeResolver::resolve($name))
            ->filter()
            ->unique('code')
            ->sortBy('name')
            ->values();

        return response()->json(['data' => $countries]);
    }

    public function services(Request $request, PricingService $pricing, ExchangeRateService $rates, CurrencyService $currencies)
    {
        $data = $request->validate([
            'provider_id' => ['required', 'uuid', 'exists:providers,id'],
            'type' => ['required', 'string'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = ProviderServiceCache::where('provider_id', $data['provider_id'])
            ->where('type', $data['type'])
            ->where('is_active', true)
            ->when($data['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"));

        if (! empty($data['country_code'])) {
            $wantedCode = strtoupper($data['country_code']);

            $matchingIds = ProviderServiceCache::where('provider_id', $data['provider_id'])
                ->where('type', $data['type'])
                ->where('is_active', true)
                ->pluck('name', 'id')
                ->filter(function ($name) use ($wantedCode) {
                    $resolved = CountryCodeResolver::resolve($name);
                    return $resolved && $resolved['code'] === $wantedCode;
                })
                ->keys();

            $query->whereIn('id', $matchingIds);
        }

        $services = $query->orderBy('name')->paginate(25, page: $data['page'] ?? 1);

        $userCurrency = $currencies->walletCurrency($request->user());

        $services->getCollection()->transform(function (ProviderServiceCache $service) use ($pricing, $rates, $userCurrency) {
            $costPriceBase = $rates->convert((float) $service->raw_rate, $service->raw_currency, 'NGN');
            $sellPriceBase = $pricing->calculateSellPrice($costPriceBase, $service->type, providerId: $service->provider_id);

            return [
                'id' => $service->id,
                'name' => $service->name,
                'unit' => $service->unit,
                'price' => round($rates->convert($sellPriceBase, 'NGN', $userCurrency), 4),
            ];
        });

        return response()->json([
            'data' => $services->items(),
            'current_page' => $services->currentPage(),
            'last_page' => $services->lastPage(),
            'total' => $services->total(),
        ]);
    }

    public function store(Request $request, PricingService $pricing, ExchangeRateService $rates, WalletService $wallet, CurrencyService $currencies)
    {
        $data = $request->validate([
            'service_id' => ['required', 'uuid', 'exists:provider_services_cache,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $service = ProviderServiceCache::with('provider')->findOrFail($data['service_id']);
        $user = $request->user();

        $costPriceInServiceCurrency = (float) $service->raw_rate * $data['quantity'];
        $costPriceBase = $rates->convert($costPriceInServiceCurrency, $service->raw_currency, 'NGN');
        $sellPriceBase = $pricing->calculateSellPrice($costPriceBase, $service->type, providerId: $service->provider_id);
        $userCurrency = $currencies->walletCurrency($user);
        $chargeInUserCurrency = $currencies->roundForCharge($rates->convert($sellPriceBase, 'NGN', $userCurrency), $userCurrency);

        try {
            $walletTx = $wallet->debit($user, $chargeInUserCurrency, TransactionType::ORDER_DEBIT, [
                'currency' => $userCurrency,
                'description' => "Order: {$service->name} x{$data['quantity']}",
            ]);
        } catch (\App\Services\InsufficientBalanceException $e) {
            return back()->with('error', 'Insufficient wallet balance. Please fund your wallet first.');
        }

        $order = Order::create([
            'user_id' => $user->id,
            'provider_id' => $service->provider_id,
            'external_service_id' => $service->external_service_id,
            'service_name' => $service->name,
            'product_type' => $service->type,
            'quantity' => $data['quantity'],
            'cost_price_snapshot' => $costPriceBase,
            'platform_price_snapshot' => $sellPriceBase,
            'charge' => $chargeInUserCurrency,
            'currency' => $userCurrency,
            'exchange_rate_snapshot' => $rates->rate('NGN', $userCurrency),
            'markup_percentage' => $pricing->getMarkupPercentage($service->type, providerId: $service->provider_id),
            'profit' => $pricing->calculateProfit($sellPriceBase, $costPriceBase),
            'channel' => OrderChannel::DIRECT,
            'status' => OrderStatus::PENDING,
        ]);

        $walletTx->update(['order_id' => $order->id]);

        ProcessProxyOrder::dispatch($order->id);

        \App\Jobs\SendTikTokPurchaseEvent::dispatch(
            $order->id,
            $user->id,
            $request->ip(),
            $request->userAgent(),
            $request->fullUrl(),
            $request->cookie('_ttp'),
        );

        return redirect()->route('orders.show', $order)->with('success', 'Order placed! We\'re provisioning it now.');
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = $request->user()->orders()
            ->when($status && in_array($status, \App\Types\OrderStatus::all(), true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('order.index', ['orders' => $orders]);
    }

    public function show(Request $request, Order $order, PricingService $pricing, ExchangeRateService $rates, CurrencyService $currencies)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $extend = null;
        $service = $this->extendableService($order);

        if ($service) {
            $quote = $this->quoteExtension($service, 1, $request->user(), $pricing, $rates, $currencies);

            $extend = [
                'unit' => $service->unit,
                'currency' => $quote['currency'],
                'unit_price' => round($quote['amount'], 4),
            ];
        }

        return view('order.show', [
            'order' => $order->load(['extensions' => fn ($q) => $q->completed()->latest()]),
            'extend' => $extend,
        ]);
    }

    /**
     * Returns the service row to price an extension against, or null if this
     * order can't be extended. Used by both show() (to render the button) and
     * extend() (to enforce it server-side).
     */
    protected function extendableService(Order $order): ?ProviderServiceCache
    {
        if ($order->status !== OrderStatus::COMPLETED
            || $order->reseller_id
            || ! $order->api_order_id
            || $order->isDataBasedProduct()) {
            return null;
        }

        $provider = $order->provider;

        if (! $provider || ! $provider->supportsExtend()) {
            return null;
        }

        return ProviderServiceCache::where('provider_id', $order->provider_id)
            ->where('external_service_id', $order->external_service_id)
            ->first();
    }

    protected function quoteExtension(
        ProviderServiceCache $service,
        int $quantity,
        $user,
        PricingService $pricing,
        ExchangeRateService $rates,
        CurrencyService $currencies,
    ): array {
        $costBase = $rates->convert((float) $service->raw_rate * $quantity, $service->raw_currency, 'NGN');
        $sellBase = $pricing->calculateSellPrice($costBase, $service->type, providerId: $service->provider_id);
        $currency = $currencies->walletCurrency($user);
        $amount   = $rates->convert($sellBase, 'NGN', $currency);

        return [
            'cost_base' => $costBase,
            'sell_base' => $sellBase,
            'profit'    => $pricing->calculateProfit($sellBase, $costBase),
            'markup'    => $pricing->getMarkupPercentage($service->type, providerId: $service->provider_id),
            'currency'  => $currency,
            'rate'      => $rates->rate('NGN', $currency),
            'amount'    => $amount,
            'charge'    => $currencies->roundForCharge($amount, $currency),
        ];
    }

    protected function extensionCharge(
        ProviderServiceCache $service,
        int $quantity,
        $user,
        PricingService $pricing,
        ExchangeRateService $rates,
        CurrencyService $currencies,
        bool $round = true,
    ): float {
        $costBase = $rates->convert((float) $service->raw_rate * $quantity, $service->raw_currency, 'NGN');
        $sellBase = $pricing->calculateSellPrice($costBase, $service->type, providerId: $service->provider_id);
        $userCurrency = $currencies->walletCurrency($user);
        $amount = $rates->convert($sellBase, 'NGN', $userCurrency);

        return $round ? $currencies->roundForCharge($amount, $userCurrency) : round($amount, 4);
    }

    public function extend(Request $request, Order $order, PricingService $pricing, ExchangeRateService $rates, WalletService $wallet, CurrencyService $currencies, ProfitService $profits)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $user = $request->user();
        $lock = Cache::lock("order-extend:{$order->id}", 60);

        if (! $lock->get()) {
            return back()->with('error', 'An extension for this order is already in progress.');
        }

        try {
            $order->refresh();
            $service = $this->extendableService($order);

            if (! $service) {
                return back()->with('error', 'This order can\'t be extended.');
            }

            $q = $this->quoteExtension($service, $data['quantity'], $user, $pricing, $rates, $currencies);
            $label = "Extension: {$order->service_name} x{$data['quantity']} (order #" . substr($order->id, 0, 8) . ')';

            try {
                $walletTx = $wallet->debit($user, $q['charge'], TransactionType::ORDER_DEBIT, [
                    'currency' => $q['currency'],
                    'description' => $label,
                    'order_id' => $order->id,
                ]);
            } catch (\App\Services\InsufficientBalanceException $e) {
                return back()->with('error', 'Insufficient wallet balance. Please fund your wallet first.');
            }

            $extension = OrderExtension::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'provider_id' => $order->provider_id,
                'quantity' => $data['quantity'],
                'cost_price_snapshot' => $q['cost_base'],
                'platform_price_snapshot' => $q['sell_base'],
                'profit' => $q['profit'],
                'markup_percentage' => $q['markup'],
                'charge' => $q['charge'],
                'currency' => $q['currency'],
                'exchange_rate_snapshot' => $q['rate'],
                'status' => 'pending',
                'wallet_transaction_id' => $walletTx->id ?? null,
            ]);

            $driver = ProxyProviderFactory::make($order->provider);

            try {
                $driver->extendOrder($order->api_order_id, ['quantity' => $data['quantity']]);
            } catch (\Throwable $e) {
                Log::warning("Extend failed for order {$order->id}: " . $e->getMessage());

                $wallet->credit($user, $q['charge'], TransactionType::ORDER_REFUND, [
                    'description' => "Refund for failed extension of order #{$order->id}",
                    'order_id' => $order->id,
                    'currency' => $q['currency'],
                ]);

                $extension->update(['status' => 'failed', 'failure_reason' => mb_substr($e->getMessage(), 0, 1000)]);

                return back()->with('error', 'The provider couldn\'t extend this order. You have not been charged.');
            }

            $extension->update(['status' => 'completed']);

            try {
                $profits->recordForExtension($extension->fresh());
            } catch (\Throwable $e) {
                Log::error("Profit ledger write failed for extension {$extension->id}: " . $e->getMessage());
            }

            try {
                $order->update([
                    'proxy_data' => $driver->listProxies($order->api_order_id),
                    'proxy_synced_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::info("Post-extend proxy refresh skipped for order {$order->id}: " . $e->getMessage());
            }

            $order->update(['provider_synced_at' => now()]);

            return back()->with('success', 'Order extended successfully.');
        } finally {
            $lock->release();
        }
    }
}