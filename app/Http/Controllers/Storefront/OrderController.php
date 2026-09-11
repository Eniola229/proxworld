<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessProxyOrder;
use App\Models\Order;
use App\Models\ProviderServiceCache;
use App\Services\ExchangeRateService;
use App\Services\PricingService;
use App\Services\WalletService;
use App\Support\CountryCodeResolver;
use App\Types\OrderChannel;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Http\Request;

/**
 * Storefront customer ordering — a customer buying from a reseller's
 * white-label panel. Deliberately separate from App\Http\Controllers\Reseller\OrderController,
 * which is the reseller buying their OWN stock from their OWN wallet at
 * wholesale price. Here, the CUSTOMER's own wallet is debited at the
 * reseller's marked-up price (customerCharge), and the reseller's markup
 * is recorded as reseller_profit for their payout balance — the reseller's
 * own wallet is never touched by a customer order.
 */
class OrderController extends Controller
{
    /**
     * Lightweight page load: just the distinct proxy types visible on this
     * reseller's storefront, no service rows, no pricing math. Actual plans
     * are fetched on demand via services().
     */
    public function create(Request $request)
    {
        $reseller = $request->attributes->get('storefront_reseller');

        $types = ProviderServiceCache::where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->distinct()
            ->pluck('type');

        return view('reseller.order.new', [
            'types' => $types,
            'reseller' => $reseller,
        ]);
    }

    /**
     * AJAX: distinct countries available for a type on this reseller's
     * storefront, resolved live from service names (no country column
     * needed). Excludes anything the reseller has hidden, same as
     * services(). Returns [code, name] pairs sorted by name.
     */
    public function countries(Request $request)
    {
        $reseller = $request->attributes->get('storefront_reseller');

        $data = $request->validate([
            'type' => ['required', 'string'],
        ]);

        $hiddenKeys = $reseller->serviceOverrides()->where('is_hidden', true)
            ->get()->map(fn ($o) => $o->provider_id.'_'.$o->external_service_id);

        $countries = ProviderServiceCache::where('type', $data['type'])
            ->where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->get(['name', 'provider_id', 'external_service_id'])
            ->reject(fn ($s) => $hiddenKeys->contains($s->provider_id.'_'.$s->external_service_id))
            ->map(fn ($s) => CountryCodeResolver::resolve($s->name))
            ->filter()
            ->unique('code')
            ->sortBy('name')
            ->values();

        return response()->json(['data' => $countries]);
    }

    /**
     * AJAX: paginated, priced (with reseller markup) services for one type,
     * optionally filtered to one country. Country is resolved live from
     * `name` on every request — nothing is stored.
     */
    public function services(Request $request, PricingService $pricing, ExchangeRateService $rates)
    {
        $reseller = $request->attributes->get('storefront_reseller');

        $data = $request->validate([
            'type' => ['required', 'string'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $hiddenKeys = $reseller->serviceOverrides()->where('is_hidden', true)
            ->get()->map(fn ($o) => $o->provider_id.'_'.$o->external_service_id);

        $wantedCode = ! empty($data['country_code']) ? strtoupper($data['country_code']) : null;

        // Hidden-service filtering is per (provider_id, external_service_id) pair,
        // which isn't a clean SQL whereNotIn — filter in PHP, same as before, but
        // now scoped to one type/search term instead of the whole catalog. Country
        // filtering is done the same way, since it isn't a real column either.
        $matching = ProviderServiceCache::with('provider')
            ->where('type', $data['type'])
            ->where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->when($data['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->get()
            ->reject(fn ($s) => $hiddenKeys->contains($s->provider_id.'_'.$s->external_service_id))
            ->when($wantedCode, fn ($collection) => $collection->filter(function ($s) use ($wantedCode) {
                $resolved = CountryCodeResolver::resolve($s->name);
                return $resolved && $resolved['code'] === $wantedCode;
            }))
            ->values();

        $perPage = 25;
        $page = $data['page'] ?? 1;
        $total = $matching->count();

        $items = $matching->slice(($page - 1) * $perPage, $perPage)->values()
            ->map(function (ProviderServiceCache $service) use ($pricing, $rates, $reseller) {
                $costPriceBase = $rates->convert((float) $service->raw_rate, $service->raw_currency, 'NGN');
                $sellPriceBase = $pricing->calculateSellPrice($costPriceBase, $service->type);
                $markupPercent = $reseller->markupPercentFor($service->provider_id, $service->external_service_id);
                $displayPrice = $sellPriceBase * (1 + $markupPercent / 100);

                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'unit' => $service->unit,
                    'provider' => $service->provider?->name ?? 'Provider',
                    'price' => round($displayPrice, 2),
                ];
            });

        return response()->json([
            'data' => $items,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
            'total' => $total,
        ]);
    }

    public function store(Request $request, PricingService $pricing, ExchangeRateService $rates, WalletService $wallet)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:provider_services_cache,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $reseller = $request->attributes->get('storefront_reseller');
        $user = $request->user();
        $service = ProviderServiceCache::with('provider')->findOrFail($data['service_id']);

        $costPriceBase = $rates->convert((float) $service->raw_rate * $data['quantity'], $service->raw_currency, 'NGN');
        $sellPriceBase = $pricing->calculateSellPrice($costPriceBase, $service->type);
        $markupPercent = $reseller->markupPercentFor($service->provider_id, $service->external_service_id);
        $customerChargeBase = $sellPriceBase * (1 + $markupPercent / 100);
        $chargeInUserCurrency = $rates->convert($customerChargeBase, 'NGN', $user->preferred_currency);

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
            'reseller_id' => $reseller->id,
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
            'markup_percentage' => $markupPercent,
            'profit' => $pricing->calculateProfit($sellPriceBase, $costPriceBase),
            'reseller_profit' => round($customerChargeBase - $sellPriceBase, 4),
            'channel' => OrderChannel::STOREFRONT,
            'status' => OrderStatus::PENDING,
        ]);

        $walletTx->update(['order_id' => $order->id]);

        ProcessProxyOrder::dispatch($order->id);

        return redirect()->route('storefront.orders.index')->with('success', 'Order placed! We\'re provisioning it now.');
    }

    public function index(Request $request)
    {
        $reseller = $request->attributes->get('storefront_reseller');
        $status = $request->query('status');

        $orders = $request->user()->orders()
            ->where('reseller_id', $reseller->id)
            ->when($status && in_array($status, \App\Types\OrderStatus::all(), true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('reseller.order.index', ['orders' => $orders]);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return view('order.show', ['order' => $order]);
    }
}