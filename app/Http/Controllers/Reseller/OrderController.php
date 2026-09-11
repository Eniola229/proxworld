<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessProxyOrder;
use App\Models\Order;
use App\Models\ProviderServiceCache;
use App\Services\ExchangeRateService;
use App\Services\ResellerWalletService;
use App\Types\OrderChannel;
use App\Types\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $reseller = $request->user()->reseller;
        $hiddenKeys = $reseller->serviceOverrides()->where('is_hidden', true)
            ->get()->map(fn ($o) => $o->provider_id.'_'.$o->external_service_id);

        $services = ProviderServiceCache::with('provider')->where('is_active', true)->get()
            ->reject(fn ($s) => $hiddenKeys->contains($s->provider_id.'_'.$s->external_service_id))
            ->map(function (ProviderServiceCache $s) use ($reseller) {
                $costPriceBase = app(ExchangeRateService::class)->convert((float) $s->raw_rate, $s->raw_currency, 'NGN');
                $resellerPrice = $costPriceBase * (1 + app(\App\Services\PricingService::class)->getMarkupPercentage($s->type, providerId: $s->provider_id) / 100);
                $markupPercent = $reseller->markupPercentFor($s->provider_id, $s->external_service_id);
                $s->display_price = round($resellerPrice * (1 + $markupPercent / 100), 2);

                return $s;
            })
            ->groupBy('type'); // App\Types\ProductType: residential, datacenter, isp, mobile

        return view('reseller.order.new', ['groupedServices' => $services, 'reseller' => $reseller]);
    }

    public function store(Request $request, ExchangeRateService $rates, ResellerWalletService $resellerWallet)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:provider_services_cache,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $reseller = $request->user()->reseller;
        $service = ProviderServiceCache::with('provider')->findOrFail($data['service_id']);

        $costPriceBase = $rates->convert((float) $service->raw_rate * $data['quantity'], $service->raw_currency, 'NGN');
        $resellerPrice = $costPriceBase * (1 + app(\App\Services\PricingService::class)->getMarkupPercentage($service->type, providerId: $service->provider_id) / 100); // platform's wholesale price to the reseller
        $markupPercent = $reseller->markupPercentFor($service->provider_id, $service->external_service_id);
        $customerCharge = $resellerPrice * (1 + $markupPercent / 100);

        try {
            $resellerWallet->debit($reseller, $resellerPrice, ['description' => "Order: {$service->name} x{$data['quantity']}"]);
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Insufficient reseller wallet balance.');
        }

        $order = Order::create([
            'user_id' => $reseller->owner_id,
            'reseller_id' => $reseller->id,
            'provider_id' => $service->provider_id,
            'external_service_id' => $service->external_service_id,
            'service_name' => $service->name,
            'product_type' => $service->type,
            'quantity' => $data['quantity'],
            'cost_price_snapshot' => $costPriceBase,
            'platform_price_snapshot' => $resellerPrice,
            'charge' => $customerCharge,
            'currency' => 'NGN',
            'exchange_rate_snapshot' => 1,
            'markup_percentage' => $markupPercent,
            'profit' => round($resellerPrice - $costPriceBase, 4), // platform's own cut, unaffected by reseller markup
            'reseller_profit' => round($customerCharge - $resellerPrice, 4), // reseller's markup, credited on completion
            'channel' => OrderChannel::RESELLER_PANEL,
            'status' => OrderStatus::PENDING,
        ]);

        ProcessProxyOrder::dispatch($order->id);

        return redirect()->route('reseller.order.index')->with('success', 'Order placed!');
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = $request->user()->reseller->orders()
            ->when($status && in_array($status, \App\Types\OrderStatus::all(), true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('reseller.order.index', ['orders' => $orders]);
    }
}