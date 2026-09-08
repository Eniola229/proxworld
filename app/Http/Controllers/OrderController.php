<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProxyOrder;
use App\Models\Order;
use App\Models\Provider;
use App\Models\ProviderServiceCache;
use App\Services\ExchangeRateService;
use App\Services\PricingService;
use App\Services\WalletService;
use App\Types\OrderChannel;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(PricingService $pricing, ExchangeRateService $rates)
    {
        $services = ProviderServiceCache::with('provider')
            ->where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->get()
            ->map(function (ProviderServiceCache $service) use ($pricing, $rates) {
                $costPriceBase = $rates->convert((float) $service->raw_rate, $service->raw_currency, 'NGN');
                $service->display_price = $pricing->calculateSellPrice($costPriceBase, $service->type);

                return $service;
            })
            ->groupBy('type'); // App\Types\ProductType: residential, datacenter, isp, mobile

        return view('order.new', ['groupedServices' => $services]);
    }

    public function store(Request $request, PricingService $pricing, ExchangeRateService $rates, WalletService $wallet)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:provider_services_cache,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $service = ProviderServiceCache::with('provider')->findOrFail($data['service_id']);
        $user = $request->user();

        $costPriceInServiceCurrency = (float) $service->raw_rate * $data['quantity'];
        $costPriceBase = $rates->convert($costPriceInServiceCurrency, $service->raw_currency, 'NGN');
        $sellPriceBase = $pricing->calculateSellPrice($costPriceBase, $service->type);
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
            'markup_percentage' => $pricing->getMarkupPercentage($service->type),
            'profit' => $pricing->calculateProfit($sellPriceBase, $costPriceBase),
            'channel' => OrderChannel::DIRECT,
            'status' => OrderStatus::PENDING,
        ]);

        $walletTx->update(['order_id' => $order->id]);

        ProcessProxyOrder::dispatch($order->id);

        return redirect()->route('orders.show', $order)->with('success', 'Order placed! We\'re provisioning it now.');
    }

    public function index(Request $request)
    {
        return view('order.index', [
            'orders' => $request->user()->orders()->latest()->paginate(15),
        ]);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return view('order.show', ['order' => $order]);
    }
}
