<?php

namespace App\Models;

use App\Types\ResellerStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reseller extends Model
{
    use HasUuids;

    protected $fillable = [
        'owner_id', 'panel_name', 'subdomain', 'custom_domain', 'logo_path', 'primary_color',
        'default_markup_percent', 'support_email', 'support_telegram', 'support_whatsapp',
        'status', 'is_suspended', 'rejection_reason', 'approved_at', 'server_ip',
        'custom_domain_status', 'custom_domain_verified_at', 'custom_domain_error', 'logo_path', 'logo_public_id',
    ];

    // Same write-protection pattern as User — only ResellerWalletService /
    // ResellerProfitService may touch these three.
    protected $guarded = ['id', 'balance', 'profit_balance', 'total_profit_earned'];

    protected function casts(): array
    {
        return [
            'default_markup_percent' => 'decimal:2',
            'balance' => 'decimal:4',
            'profit_balance' => 'decimal:4',
            'total_profit_earned' => 'decimal:4',
            'is_suspended' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function customers(): HasMany
    {
        return $this->hasMany(User::class, 'reseller_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function serviceOverrides(): HasMany
    {
        return $this->hasMany(ResellerServiceOverride::class);
    }

    public function wallet(): HasMany
    {
        return $this->hasMany(ResellerWalletTransaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(ResellerWithdrawal::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ! $this->is_suspended;
    }

    /** Markup % to apply for a given provider service, honoring per-plan overrides. */
    public function markupPercentFor(string $providerId, string $externalServiceId): float
    {
        $override = $this->serviceOverrides()
            ->where('provider_id', $providerId)
            ->where('external_service_id', $externalServiceId)
            ->first();

        return (float) ($override?->markup_percent ?? $this->default_markup_percent);
    }

    /** Turns a free-text Telegram handle/link into a clickable t.me URL. */
    public function getTelegramLinkAttribute(): ?string
    {
        if (!$this->support_telegram) {
            return null;
        }

        $value = trim($this->support_telegram);

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return 'https://t.me/' . ltrim($value, '@');
    }

    /** Turns a free-text WhatsApp number/link into a clickable wa.me URL. */
    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->support_whatsapp) {
            return null;
        }

        $value = trim($this->support_whatsapp);

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        $digits = preg_replace('/\D/', '', $value);

        return $digits ? 'https://wa.me/' . $digits : null;
    }

    public function profitTransactions(): HasMany
    {
        return $this->hasMany(ResellerProfitTransaction::class);
    }

    /**
     * Lifetime profit earned from orders — NOT the current withdrawable
     * balance (that's $reseller->profit_balance). This is a running total
     * of every profit credit ever posted, regardless of what's since been
     * withdrawn or spent.
     */
    public function totalProfitEarned(): float
    {
        return (float) $this->profitTransactions()
            ->where('type', 'credit')
            ->sum('amount');
    }

       /**
         * Withdrawable profit balance — what's actually available to cash out,
         * as opposed to totalProfitEarned() which is the lifetime running total
         * regardless of what's since been withdrawn.
         */
        public function availableProfitBalance(): float
        {
            return (float) $this->profit_balance;
        }
}