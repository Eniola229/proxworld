<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderServiceCache;
use App\Services\CurrencyService;
use App\Services\ExchangeRateService;
use App\Services\PricingService;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    public function index(Request $request, PricingService $pricing, ExchangeRateService $rates, CurrencyService $currencies)
    {
        $currency = $currencies->walletCurrency($request->user());

        $services = ProviderServiceCache::where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->get(['id', 'name', 'type', 'unit', 'raw_rate', 'raw_currency', 'provider_id']);

        $services->each(function (ProviderServiceCache $service) use ($pricing, $rates, $currency) {
            $costNgn = $rates->convert((float) $service->raw_rate, $service->raw_currency, 'NGN');
            $markupPercent = $pricing->getMarkupPercentage($service->type, providerId: $service->provider_id);
            $sellNgn = $costNgn * (1 + $markupPercent / 100);

            $service->raw_rate = round($rates->convert($sellNgn, 'NGN', $currency), 6);
            $service->raw_currency = $currency;
        })->makeHidden('provider_id');

        return response()->json(['currency' => $currency, 'data' => $services]);
    }
}