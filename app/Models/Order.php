<?php

namespace App\Models;

use App\Types\OrderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'reseller_id', 'provider_id', 'api_order_id', 'external_service_id',
        'service_name', 'product_type', 'quantity', 'cost_price_snapshot', 'platform_price_snapshot',
        'charge', 'currency', 'exchange_rate_snapshot', 'markup_percentage', 'profit', 'reseller_profit',
        'channel', 'status', 'admin_note', 'failure_reason', 'retry_count', 'provider_synced_at',
        'proxy_data', 'proxy_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'cost_price_snapshot' => 'decimal:6',
            'platform_price_snapshot' => 'decimal:6',
            'charge' => 'decimal:4',
            'exchange_rate_snapshot' => 'decimal:8',
            'markup_percentage' => 'decimal:2',
            'profit' => 'decimal:4',
            'reseller_profit' => 'decimal:4',
            'provider_synced_at' => 'datetime',
            'proxy_data' => 'array',
            'proxy_synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED, OrderStatus::REFUNDED]);
    }

    /** True when we hold per-order proxy credentials — false for data-based products like Residential/Mobile. */
    public function hasProxyCredentials(): bool
    {
        return ! empty($this->proxy_data);
    }

    /** Data-based products (Residential, Mobile) use one static account-wide gateway instead of per-order credentials. */
    public function isDataBasedProduct(): bool
    {
        return in_array($this->product_type, ['residential', 'mobile']);
    }
}