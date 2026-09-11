<?php

namespace Database\Seeders;

use App\Models\PricingRule;
use App\Models\Setting;
use App\Types\ProductType;
use Illuminate\Database\Seeder;

/**
 * Seeds sane starting pricing config so a fresh install isn't running on
 * empty defaults. Safe to re-run — Setting::set() overwrites the single
 * 'pricing_config' row, and the product-type rules use updateOrCreate()
 * keyed on scope_type+product_type so re-running won't duplicate them.
 */
class PricingSettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('pricing_config', [
            'default_markup' => 30,
            'minimum_markup' => 10,
            'maximum_markup' => 200,
            'currency_buffer' => 3,
            'round_prices' => true,
        ]);

        // Starting per-product-type markups — tune these from Settings > Pricing
        // once you know your real margins per type. Provider-specific and
        // protocol-specific rules are left for you to add via the admin UI,
        // since they depend on which providers you've actually added.
        $defaultsByType = [
            ProductType::RESIDENTIAL => 40,
            ProductType::ISP => 30,
            ProductType::DATACENTER => 25,
            ProductType::MOBILE => 50,
        ];

        foreach ($defaultsByType as $type => $markup) {
            PricingRule::updateOrCreate(
                ['scope_type' => 'product_type', 'product_type' => $type, 'protocol' => null, 'provider_id' => null],
                ['markup_percentage' => $markup, 'is_active' => true, 'notes' => 'Seeded default']
            );
        }
    }
}