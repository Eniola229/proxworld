<?php

namespace App\Services;

use App\Models\PricingRule;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Turns a provider's raw cost (in their currency, usually USD) into what we
 * charge — direct customers, resellers, and end-customers-of-a-reseller all
 * go through here, so the markup logic lives in exactly one place.
 *
 * Global numbers (default/min/max markup, currency buffer, rounding) live in
 * Setting::get('pricing_config') — edited on Settings > Pricing.
 *
 * Specific overrides live in the `pricing_rules` table so new rules can be
 * added freely from the admin UI without touching code. Checked most-specific
 * first:
 *   1. provider_product_type — this exact provider AND this exact product type
 *   2. provider               — this exact provider, any product type
 *   3. combined                — this product type AND this protocol
 *   4. protocol                — this protocol, any product type
 *   5. product_type            — this product type, any protocol
 *   6. (none matched) default_markup from global settings
 * currency_buffer is always added on top, then clamped to [minimum_markup, maximum_markup].
 */
class PricingService
{
    protected function globalConfig(): array
    {
        return Setting::get('pricing_config', [
            'default_markup' => 30,
            'minimum_markup' => 10,
            'maximum_markup' => 200,
            'currency_buffer' => 3,
            'round_prices' => true,
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, PricingRule> */
    protected function activeRules()
    {
        return Cache::rememberForever(
            'pricing_rules.active',
            fn () => PricingRule::where('is_active', true)->get()
        );
    }

    public function getMarkupPercentage(?string $productType = null, ?string $protocol = null, ?string $providerId = null): float
    {
        $config = $this->globalConfig();
        $rules = $this->activeRules();
        $markup = null;

        if ($providerId && $productType) {
            $rule = $rules->first(fn ($r) => $r->scope_type === 'provider_product_type'
                && $r->provider_id === $providerId && $r->product_type === $productType);
            $markup ??= $rule?->markup_percentage !== null ? (float) $rule->markup_percentage : null;
        }

        if ($markup === null && $providerId) {
            $rule = $rules->first(fn ($r) => $r->scope_type === 'provider' && $r->provider_id === $providerId);
            $markup ??= $rule?->markup_percentage !== null ? (float) $rule->markup_percentage : null;
        }

        if ($markup === null && $productType && $protocol) {
            $rule = $rules->first(fn ($r) => $r->scope_type === 'combined'
                && $r->product_type === $productType && $r->protocol === $protocol);
            $markup ??= $rule?->markup_percentage !== null ? (float) $rule->markup_percentage : null;
        }

        if ($markup === null && $protocol) {
            $rule = $rules->first(fn ($r) => $r->scope_type === 'protocol' && $r->protocol === $protocol);
            $markup ??= $rule?->markup_percentage !== null ? (float) $rule->markup_percentage : null;
        }

        if ($markup === null && $productType) {
            $rule = $rules->first(fn ($r) => $r->scope_type === 'product_type' && $r->product_type === $productType);
            $markup ??= $rule?->markup_percentage !== null ? (float) $rule->markup_percentage : null;
        }

        $markup ??= (float) $config['default_markup'];
        $markup += (float) $config['currency_buffer'];

        return max((float) $config['minimum_markup'], min((float) $config['maximum_markup'], $markup));
    }

    /** Cost is in base currency (post exchange-rate conversion) — returns the sell price. */
    public function calculateSellPrice(float $costPrice, ?string $productType = null, ?string $protocol = null, ?string $providerId = null): float
    {
        $markup = $this->getMarkupPercentage($productType, $protocol, $providerId);
        $price = $costPrice * (1 + $markup / 100);

        return $this->globalConfig()['round_prices'] ? round($price, 2) : round($price, 4);
    }

    public function calculateProfit(float $charge, float $costPrice): float
    {
        return round($charge - $costPrice, 4);
    }

    public function getProfitBreakdown(float $charge, float $costPrice, ?string $productType = null, ?string $providerId = null): array
    {
        $profit = $this->calculateProfit($charge, $costPrice);

        return [
            'charge' => $charge,
            'cost_price' => $costPrice,
            'profit' => $profit,
            'margin_percent' => $costPrice > 0 ? round(($profit / $costPrice) * 100, 2) : 0,
            'markup_used' => $this->getMarkupPercentage($productType, providerId: $providerId),
        ];
    }
}