<?php

namespace App\Http\Controllers\Api;

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

class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->where('channel', OrderChannel::PUBLIC_API)
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return response()->json(['data' => $order]);
    }

    public function store(Request $request, PricingService $pricing, ExchangeRateService $rates, WalletService $wallet)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:provider_services_cache,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $user = $request->user();
        $service = ProviderServiceCache::with('provider')->findOrFail($data['service_id']);

        $costPriceBase = $rates->convert((float) $service->raw_rate * $data['quantity'], $service->raw_currency, 'NGN');
        $sellPriceBase = $pricing->calculateSellPrice($costPriceBase, $service->type);
        $charge = $rates->convert($sellPriceBase, 'NGN', $user->preferred_currency);

        try {
            $walletTx = $wallet->debit($user, $charge, TransactionType::ORDER_DEBIT, [
                'currency' => $user->preferred_currency,
                'description' => "API order: {$service->name} x{$data['quantity']}",
            ]);
        } catch (\App\Services\InsufficientBalanceException $e) {
            return response()->json(['message' => 'Insufficient wallet balance.'], 402);
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
            'charge' => $charge,
            'currency' => $user->preferred_currency,
            'exchange_rate_snapshot' => $rates->rate('NGN', $user->preferred_currency),
            'markup_percentage' => $pricing->getMarkupPercentage($service->type),
            'profit' => $pricing->calculateProfit($sellPriceBase, $costPriceBase),
            'channel' => OrderChannel::PUBLIC_API,
            'status' => OrderStatus::PENDING,
        ]);

        $walletTx->update(['order_id' => $order->id]);
        ProcessProxyOrder::dispatch($order->id);

        return response()->json(['data' => $order], 201);
    }
}
