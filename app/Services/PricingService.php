<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Turns a provider's raw cost (in their currency, usually USD) into what we
 * charge — direct customers, resellers, and end-customers-of-a-reseller all
 * go through here, so the markup logic lives in exactly one place.
 *
 * Config shape (Setting::get('pricing_config')), matching the admin
 * Settings > Pricing screen:
 *   default_markup            float   e.g. 30  (percent)
 *   minimum_markup            float
 *   maximum_markup            float
 *   currency_buffer           float   extra % added on top to absorb forex swings
 *   round_prices              bool
 *   service_type_markup       [ProductType => percent]   overrides default_markup per product type
 *   platform_markup           [protocol => percent]      overrides for http/socks5/socks4, applied additively
 */
class PricingService
{
    protected function config(): array
    {
        return Setting::get('pricing_config', [
            'default_markup' => 30,
            'minimum_markup' => 10,
            'maximum_markup' => 200,
            'currency_buffer' => 3,
            'round_prices' => true,
            'service_type_markup' => [],
            'platform_markup' => [],
        ]);
    }

    public function getMarkupPercentage(?string $productType = null, ?string $protocol = null): float
    {
        $config = $this->config();
        $markup = (float) ($config['service_type_markup'][$productType] ?? $config['default_markup']);

        if ($protocol && isset($config['platform_markup'][$protocol])) {
            $markup += (float) $config['platform_markup'][$protocol];
        }

        $markup += (float) $config['currency_buffer'];

        return max($config['minimum_markup'], min($config['maximum_markup'], $markup));
    }

    /** Cost is in base currency (post exchange-rate conversion) — returns the sell price. */
    public function calculateSellPrice(float $costPrice, ?string $productType = null, ?string $protocol = null): float
    {
        $markup = $this->getMarkupPercentage($productType, $protocol);
        $price = $costPrice * (1 + $markup / 100);

        return $this->config()['round_prices'] ? round($price, 2) : round($price, 4);
    }

    public function calculateProfit(float $charge, float $costPrice): float
    {
        return round($charge - $costPrice, 4);
    }

    public function getProfitBreakdown(float $charge, float $costPrice, ?string $productType = null): array
    {
        $profit = $this->calculateProfit($charge, $costPrice);

        return [
            'charge' => $charge,
            'cost_price' => $costPrice,
            'profit' => $profit,
            'margin_percent' => $costPrice > 0 ? round(($profit / $costPrice) * 100, 2) : 0,
            'markup_used' => $this->getMarkupPercentage($productType),
        ];
    }
}
