<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public-facing shape of an Order — used by the API only. Deliberately
 * excludes everything internal: cost_price_snapshot, platform_price_snapshot,
 * profit, reseller_profit, markup_percentage, exchange_rate_snapshot,
 * admin_note, failure_reason, retry_count, provider_id/provider relation.
 * The customer only ever needs to see what they paid and what they got.
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_name' => $this->service_name,
            'product_type' => $this->product_type,
            'quantity' => $this->quantity,
            'charge' => (float) $this->charge,
            'currency' => $this->currency,
            'status' => $this->status,
            'api_order_id' => $this->api_order_id,
            'proxy_access' => $this->when($this->hasProxyCredentials(), fn () => $this->proxy_data),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}