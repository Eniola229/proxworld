<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePricingSettingsRequest;
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
            'service_type_markup' => [],
            'platform_markup' => [],
            'combined_markup' => [],
        ];

        $pricing = array_merge($defaults, Setting::get('pricing_config', []));

        return view('admin.settings.pricing', [
            'pricing' => $pricing,
            'productTypes' => ProductType::all(),
        ]);
    }
    public function update(UpdatePricingSettingsRequest $request)
    {
        $data = $request->validated();
        $data['round_prices'] = $request->boolean('round_prices');
        $data['service_type_markup'] = array_filter($data['service_type_markup'] ?? [], fn ($v) => $v !== null && $v !== '');
        $data['platform_markup'] = array_filter($data['platform_markup'] ?? [], fn ($v) => $v !== null && $v !== '');
        $data['combined_markup'] = array_filter($data['combined_markup'] ?? [], fn ($v) => $v !== null && $v !== '');

        Setting::set('pricing_config', $data);

        return back()->with('success', 'Pricing settings updated. New prices apply to orders placed from now on — historical order profit figures are unaffected.');
    }
}
