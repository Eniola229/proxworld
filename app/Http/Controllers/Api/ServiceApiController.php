<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderServiceCache;
use App\Services\PricingService;

class ServiceApiController extends Controller
{
    public function index(PricingService $pricing)
    {
        // provider_id is only needed to look up the right markup rule below —
        // it's stripped back out before the response goes out.
        $services = ProviderServiceCache::where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->get(['id', 'name', 'type', 'unit', 'raw_rate', 'raw_currency', 'provider_id']);

        $services->each(function (ProviderServiceCache $service) use ($pricing) {
            // Same markup logic /api/orders uses to calculate the actual
            // charge (PricingService::calculateSellPrice) — applied here too
            // so this listing never quotes the bare provider cost. We don't
            // reuse calculateSellPrice() directly because it rounds to 2
            // decimals for display, which would flatten a small per-unit
            // rate like 0.000300 down to 0.00; raw_rate keeps its native
            // 6-decimal precision instead.
            $markupPercent = $pricing->getMarkupPercentage($service->type, providerId: $service->provider_id);
            $service->raw_rate = round((float) $service->raw_rate * (1 + $markupPercent / 100), 6);
        })->makeHidden('provider_id');

        return response()->json(['data' => $services]);
    }
}
