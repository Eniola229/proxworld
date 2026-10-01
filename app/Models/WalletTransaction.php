<?php

namespace App\Models;

use App\Services\ExchangeRateService;
use App\Types\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Append-only. Rows are only ever created by App\Services\WalletService. */
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

    /** Hides the "currency switched" rows from money totals (they aren't deposits or spend). */
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

    /** ADMIN DISPLAY ONLY: re-expresses amounts in NGN on this in-memory instance. Never save() afterwards. */
    public function withNgnAmounts(): static
    {
        $rates = app(ExchangeRateService::class);
        $cur = $this->currency ?: 'NGN';
        $beforeCur = $this->balanceBeforeCurrency();

        $this->setAttribute('amount', $rates->convert((float) $this->amount, $cur, 'NGN'));
        $this->setAttribute('balance_before', $rates->convert((float) $this->balance_before, $beforeCur, 'NGN'));
        $this->setAttribute('balance_after', $rates->convert((float) $this->balance_after, $cur, 'NGN'));
        $this->setAttribute('currency', 'NGN');

        return $this;
    }
}