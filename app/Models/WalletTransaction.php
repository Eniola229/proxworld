<?php

namespace App\Models;

use App\Services\ExchangeRateService;
use App\Types\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only. Rows here are only ever created by App\Services\WalletService.
 * Never update a row's amount/balance_before/balance_after after the fact —
 * if a correction is needed, create a new offsetting transaction instead,
 * so the ledger always reflects exactly what happened and when.
 */
class WalletTransaction extends Model
{
    use HasUuids;

    protected $table = 'wallet_transactions';

    protected $fillable = [
        'user_id', 'reference', 'type', 'purpose', 'amount', 'balance_before',
        'balance_after', 'currency', 'payment_method', 'status', 'description', 'meta', 'order_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:4',
            'balance_before' => 'decimal:4',
            'balance_after' => 'decimal:4',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Hides the internal "wallet currency switched" rows from money totals (they aren't deposits or spend). */
    public function scopeExcludingSwitches(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->whereNull('purpose')->orWhere('purpose', '!=', TransactionType::CURRENCY_SWITCH));
    }

    public function isCurrencySwitch(): bool
    {
        return $this->purpose === TransactionType::CURRENCY_SWITCH;
    }

    /** balance_before is in the OLD currency on a switch row; everywhere else it matches `currency`. */
    public function balanceBeforeCurrency(): string
    {
        if ($this->isCurrencySwitch()) {
            return $this->meta['from_currency'] ?? ($this->currency ?: 'NGN');
        }

        return $this->currency ?: 'NGN';
    }

    /** NGN value of this row's amount (for page totals). Does not touch the real `amount`. */
    public function getAmountNgnAttribute(): float
    {
        return app(ExchangeRateService::class)->convert((float) $this->amount, $this->currency ?: 'NGN', 'NGN');
    }
}