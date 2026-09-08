<?php

namespace App\Models;

use App\Types\ResellerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reseller extends Model
{
    protected $fillable = [
        'owner_id', 'panel_name', 'subdomain', 'custom_domain', 'logo_path', 'primary_color',
        'default_markup_percent', 'support_email', 'support_telegram', 'support_whatsapp',
        'status', 'is_suspended', 'rejection_reason', 'approved_at', 'server_ip',
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

    protected static function booted(): void
    {
        static::creating(function (Reseller $r) {
            $r->uuid ??= (string) \Illuminate\Support\Str::uuid();
        });
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

    public function profitTransactions(): HasMany
    {
        return $this->hasMany(ResellerProfitTransaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(ResellerWithdrawal::class);
    }

    public function isActive(): bool
    {
        return $this->status === ResellerStatus::APPROVED && ! $this->is_suspended;
    }

    /** Markup % to apply for a given provider service, honoring per-plan overrides. */
    public function markupPercentFor(int $providerId, string $externalServiceId): float
    {
        $override = $this->serviceOverrides()
            ->where('provider_id', $providerId)
            ->where('external_service_id', $externalServiceId)
            ->first();

        return (float) ($override?->markup_percent ?? $this->default_markup_percent);
    }
}
