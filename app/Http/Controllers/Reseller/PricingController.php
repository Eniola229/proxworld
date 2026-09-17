<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reseller\UpdateResellerPricingRequest;
use App\Models\Provider;
use App\Models\ResellerServiceOverride;
use App\Services\ExchangeRateService;
use App\Services\PricingService;
use Illuminate\Http\Request;

/**
 * A reseller's own storefront pricing — their default markup on top of the
 * platform's reseller price, plus per-plan overrides. Never lets them go
 * below the platform's configured minimum markup floor (enforced in
 * UpdateResellerPricingRequest), and never touches admin's own pricing_config.
 */
class PricingController extends Controller
{
    public function edit(Request $request, PricingService $pricing, ExchangeRateService $exchangeRates)
    {
        $reseller = $request->user()->reseller;

        $services = Provider::active()->with('services')->get()
            ->flatMap(fn ($provider) => $provider->services->map(function ($service) use ($provider) {
                $service->provider_id = $provider->id;
                $service->provider_name = $provider->name;

                return $service;
            }))
            ->map(function ($service) use ($pricing, $exchangeRates, $reseller) {
                // raw_rate is in the provider's own currency — convert to NGN
                // before any markup math touches it.
                $costPriceNgn = $exchangeRates->convert(
                    (float) $service->raw_rate,
                    $service->raw_currency,
                    'NGN'
                );

                // What the PLATFORM charges this reseller (platform markup,
                // scoped by product type/protocol/provider — see PricingService).
                $basePrice = $pricing->calculateSellPrice(
                    $costPriceNgn,
                    $service->type ?? null,
                    $service->protocol ?? null,
                    $service->provider_id
                );

                // What THIS reseller charges their own customer — reseller's
                // markup (override or their default_markup_percent) on top
                // of the platform's price, never on top of raw provider cost.
                $resellerMarkup = (float) $reseller->markupPercentFor(
                    $service->provider_id,
                    $service->external_service_id
                );
                $yourPrice = round($basePrice * (1 + $resellerMarkup / 100), 2);

                $service->base_price = $basePrice;
                $service->reseller_markup = $resellerMarkup;
                $service->your_price = $yourPrice;

                return $service;
            });

        $overrides = $reseller->serviceOverrides()->get()
            ->keyBy(fn ($o) => $o->provider_id.'_'.$o->external_service_id);

        return view('reseller.manage.services', [
            'reseller' => $reseller,
            'services' => $services,
            'overrides' => $overrides,
        ]);
    }

    public function update(UpdateResellerPricingRequest $request)
    {
        $reseller = $request->user()->reseller;

        $reseller->forceFill([
            'default_markup_percent' => $request->input('default_markup_percent'),
        ])->save(); // not a guarded balance field — plain attribute, direct save is fine here

        $updatedCount = 0;

        foreach ((array) $request->input('markups', []) as $key => $markup) {
            [$providerId, $externalServiceId] = array_pad(explode('_', $key, 2), 2, null);

            if (! $providerId || ! $externalServiceId) {
                continue;
            }

            ResellerServiceOverride::updateOrCreate(
                [
                    'reseller_id' => $reseller->id,
                    'provider_id' => $providerId,
                    'external_service_id' => $externalServiceId,
                ],
                [
                    'markup_percent' => ($markup === null || $markup === '') ? null : $markup,
                    'is_hidden' => in_array($key, (array) $request->input('hidden', [])),
                ]
            );

            $updatedCount++;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your storefront pricing has been updated.',
                'total_services' => $updatedCount,
            ]);
        }

        return back()->with('success', 'Your storefront pricing has been updated.');
    }
}