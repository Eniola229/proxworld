<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessProxyOrder;
use App\Models\Order;
use App\Models\ProviderServiceCache;
use App\Services\ExchangeRateService;
use App\Services\PricingService;
use App\Services\WalletService;
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
    public function create(Request $request, PricingService $pricing, ExchangeRateService $rates)
    {
        $reseller = $request->attributes->get('storefront_reseller');

        $hiddenKeys = $reseller->serviceOverrides()->where('is_hidden', true)
            ->get()->map(fn ($o) => $o->provider_id.'_'.$o->external_service_id);

        $services = ProviderServiceCache::with('provider')
            ->where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->get()
            ->reject(fn ($s) => $hiddenKeys->contains($s->provider_id.'_'.$s->external_service_id))
            ->map(function (ProviderServiceCache $s) use ($pricing, $rates, $reseller) {
                $costPriceBase = $rates->convert((float) $s->raw_rate, $s->raw_currency, 'NGN');
                $sellPriceBase = $pricing->calculateSellPrice($costPriceBase, $s->type);
                $markupPercent = $reseller->markupPercentFor($s->provider_id, $s->external_service_id);
                $s->display_price = round($sellPriceBase * (1 + $markupPercent / 100), 2);

                return $s;
            })
            ->groupBy('type'); // App\Types\ProductType: residential, datacenter, isp, mobile

        return view('reseller.order.new', ['groupedServices' => $services, 'reseller' => $reseller]);
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
        $sellPriceBase = $pricing->calculateSellPrice($costPriceBase, $service->type); // platform's price — same as a direct customer would pay, no reseller markup yet
        $markupPercent = $reseller->markupPercentFor($service->provider_id, $service->external_service_id);
        $customerChargeBase = $sellPriceBase * (1 + $markupPercent / 100); // what THIS customer actually pays, in NGN
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
            'markup_percentage' => $markupPercent, // reseller's own markup, matching Reseller\OrderController's convention
            'profit' => $pricing->calculateProfit($sellPriceBase, $costPriceBase), // platform's cut — unaffected by reseller markup
            'reseller_profit' => round($customerChargeBase - $sellPriceBase, 4), // reseller's cut, credited to profit_balance on completion
            'channel' => OrderChannel::STOREFRONT, // add this constant to App\Types\OrderChannel
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
            ->where('reseller_id', $reseller->id) // customer sees only their OWN orders, not the whole reseller's order book
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