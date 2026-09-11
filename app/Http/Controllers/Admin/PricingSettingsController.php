<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePricingSettingsRequest;
use App\Models\PricingRule;
use App\Models\Provider;
use App\Models\Setting;
use App\Types\ProductType;

class PricingSettingsController extends Controller
{
    public function edit()
    {
        $defaults = [
            'default_markup' => 30,
            'minimum_markup' => 10,
            'maximum_markup' => 200,
            'currency_buffer' => 3,
            'round_prices' => true,
        ];

        $pricing = array_merge($defaults, Setting::get('pricing_config', []));

        return view('admin.settings.pricing', [
            'pricing' => $pricing,
            'productTypes' => ProductType::all(),
            'providers' => Provider::orderBy('name')->get(),
            'rules' => PricingRule::with('provider')->latest()->get(),
        ]);
    }

    public function update(UpdatePricingSettingsRequest $request)
    {
        $data = $request->validated();
        $data['round_prices'] = $request->boolean('round_prices');

        Setting::set('pricing_config', $data);

        return back()->with('success', 'Pricing settings updated. New prices apply to orders placed from now on — historical order profit figures are unaffected.');
    }
}