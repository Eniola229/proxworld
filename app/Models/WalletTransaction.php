<?php

namespace App\Models;

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

    protected static function booted(): void
    {
        static::creating(function (WalletTransaction $t) {
            $t->uuid ??= (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
