<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reseller\UpdateResellerPricingRequest;
use App\Models\Provider;
use App\Models\ResellerServiceOverride;
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
    public function edit(Request $request, PricingService $pricing)
    {
        $reseller = $request->user()->reseller;

        $services = Provider::active()->with('services')->get()
            ->flatMap(fn ($provider) => $provider->services->map(function ($service) use ($provider) {
                $service->provider_id = $provider->id;
                $service->provider_name = $provider->name;

                return $service;
            }));

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
        }

        return back()->with('success', 'Your storefront pricing has been updated.');
    }
}
