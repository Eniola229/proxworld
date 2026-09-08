<?php

namespace App\Services;

use App\Models\Reseller;
use App\Models\ResellerWalletTransaction;
use App\Types\WalletTransactionDirection as Direction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/** The ONLY code path allowed to change resellers.balance. Mirrors WalletService exactly. */
class ResellerWalletService
{
    public function credit(Reseller $reseller, float $amount, array $options = []): ResellerWalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be positive.');
        }

        return DB::transaction(function () use ($reseller, $amount, $options) {
            $locked = Reseller::whereKey($reseller->id)->lockForUpdate()->firstOrFail();
            $before = (float) $locked->balance;
            $after = round($before + $amount, 4);
            $locked->forceFill(['balance' => $after])->save();

            return ResellerWalletTransaction::create([
                'reseller_id' => $locked->id,
                'reference' => $options['reference'] ?? ('RWTX-'.strtoupper(Str::random(12))),
                'type' => Direction::CREDIT,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $options['currency'] ?? 'NGN',
                'payment_method' => $options['payment_method'] ?? null,
                'status' => $options['status'] ?? 'success',
                'description' => $options['description'] ?? null,
                'order_id' => $options['order_id'] ?? null,
            ]);
        });
    }

    public function debit(Reseller $reseller, float $amount, array $options = []): ResellerWalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be positive.');
        }

        return DB::transaction(function () use ($reseller, $amount, $options) {
            $locked = Reseller::whereKey($reseller->id)->lockForUpdate()->firstOrFail();
            $before = (float) $locked->balance;

            if ($before < $amount) {
                throw new RuntimeException("Reseller #{$locked->id} has insufficient wallet balance.");
            }

            $after = round($before - $amount, 4);
            $locked->forceFill(['balance' => $after])->save();

            return ResellerWalletTransaction::create([
                'reseller_id' => $locked->id,
                'reference' => $options['reference'] ?? ('RWTX-'.strtoupper(Str::random(12))),
                'type' => Direction::DEBIT,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $options['currency'] ?? 'NGN',
                'payment_method' => $options['payment_method'] ?? null,
                'status' => $options['status'] ?? 'success',
                'description' => $options['description'] ?? null,
                'order_id' => $options['order_id'] ?? null,
            ]);
        });
    }
}
