<?php

namespace App\Services;

use App\Models\Reseller;
use App\Models\ResellerProfitTransaction;
use App\Types\ProfitTransactionType;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The ONLY code path allowed to change resellers.profit_balance (their
 * withdrawable markup earnings). Same locked-transaction + ledger pattern
 * as WalletService.
 */
class ResellerProfitService
{
    public function credit(Reseller $reseller, float $amount, string $type, array $options = []): ResellerProfitTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be positive.');
        }

        return DB::transaction(function () use ($reseller, $amount, $type, $options) {
            $locked = Reseller::whereKey($reseller->id)->lockForUpdate()->firstOrFail();
            $before = (float) $locked->profit_balance;
            $after = round($before + $amount, 4);

            $locked->forceFill([
                'profit_balance' => $after,
                'total_profit_earned' => $type === ProfitTransactionType::ORDER_MARKUP
                    ? round((float) $locked->total_profit_earned + $amount, 4)
                    : $locked->total_profit_earned,
            ])->save();

            return ResellerProfitTransaction::create([
                'reseller_id' => $locked->id,
                'order_id' => $options['order_id'] ?? null,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => $options['description'] ?? null,
            ]);
        });
    }

    /** Used when a reseller submits a withdrawal request — reserves funds immediately. */
    public function debit(Reseller $reseller, float $amount, string $type, array $options = []): ResellerProfitTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be positive.');
        }

        return DB::transaction(function () use ($reseller, $amount, $type, $options) {
            $locked = Reseller::whereKey($reseller->id)->lockForUpdate()->firstOrFail();
            $before = (float) $locked->profit_balance;

            if ($before < $amount) {
                throw new RuntimeException("Reseller #{$locked->id} has insufficient profit balance.");
            }

            $after = round($before - $amount, 4);
            $locked->forceFill(['profit_balance' => $after])->save();

            return ResellerProfitTransaction::create([
                'reseller_id' => $locked->id,
                'order_id' => $options['order_id'] ?? null,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => $options['description'] ?? null,
            ]);
        });
    }
}
