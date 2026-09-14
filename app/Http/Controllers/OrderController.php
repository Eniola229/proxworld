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

class OrderController extends Controller
{
    public function create()
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

        return view('order.new', ['providersByType' => $providersByType]);
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

    public function services(Request $request, PricingService $pricing, ExchangeRateService $rates)
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

        $services->getCollection()->transform(function (ProviderServiceCache $service) use ($pricing, $rates) {
            $costPriceBase = $rates->convert((float) $service->raw_rate, $service->raw_currency, 'NGN');

            return [
                'id' => $service->id,
                'name' => $service->name,
                'unit' => $service->unit,
                'price' => round($pricing->calculateSellPrice($costPriceBase, $service->type, providerId: $service->provider_id), 2),
            ];
        });

        return response()->json([
            'data' => $services->items(),
            'current_page' => $services->currentPage(),
            'last_page' => $services->lastPage(),
            'total' => $services->total(),
        ]);
    }

    public function store(Request $request, PricingService $pricing, ExchangeRateService $rates, WalletService $wallet)
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
        $chargeInUserCurrency = $rates->convert($sellPriceBase, 'NGN', $user->preferred_currency);

        try {
            $walletTx = $wallet->debit($user, $chargeInUserCurrency, TransactionType::ORDER_DEBIT, [
                'currency' => $user->preferred_currency,
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
            'currency' => $user->preferred_currency,
            'exchange_rate_snapshot' => $rates->rate('NGN', $user->preferred_currency),
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

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return view('order.show', ['order' => $order]);
    }
}