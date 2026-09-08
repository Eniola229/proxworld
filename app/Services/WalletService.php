<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Types\WalletTransactionDirection as Direction;
use App\Types\WalletTransactionStatus as Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * The ONLY code path allowed to change users.balance. Never touch
 * $user->balance = ... anywhere else in the app — always come through here.
 *
 * Every call:
 *   1. Opens a DB transaction.
 *   2. Row-locks the user (lockForUpdate) so two simultaneous requests
 *      (e.g. a top-up webhook + an order debit racing each other) can't
 *      read a stale balance and both "succeed" against funds that only
 *      exist once.
 *   3. Writes an append-only ledger row mirroring the change.
 *   4. Updates the cached total on the user row.
 */
class WalletService
{
    public function credit(User $user, float $amount, string $purpose, array $options = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be positive.');
        }

        return DB::transaction(function () use ($user, $amount, $purpose, $options) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $before = (float) $locked->balance;
            $after = round($before + $amount, 4);

            $locked->forceFill(['balance' => $after])->save();

            return WalletTransaction::create([
                'user_id' => $locked->id,
                'reference' => $options['reference'] ?? ('WTX-'.strtoupper(Str::random(12))),
                'type' => Direction::CREDIT,
                'purpose' => $purpose,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $options['currency'] ?? 'NGN',
                'payment_method' => $options['payment_method'] ?? null,
                'status' => $options['status'] ?? Status::SUCCESS,
                'description' => $options['description'] ?? null,
                'meta' => $options['meta'] ?? null,
                'order_id' => $options['order_id'] ?? null,
            ]);
        });
    }

    /**
     * @throws InsufficientBalanceException if the user can't cover $amount.
     * Callers MUST catch this — it's the real security boundary (the
     * EnsureBalanceIsPositive middleware is only a friendly early exit).
     */
    public function debit(User $user, float $amount, string $purpose, array $options = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be positive.');
        }

        return DB::transaction(function () use ($user, $amount, $purpose, $options) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $before = (float) $locked->balance;

            if ($before < $amount) {
                throw new InsufficientBalanceException($locked, $amount, $before);
            }

            $after = round($before - $amount, 4);

            $locked->forceFill(['balance' => $after])->save();

            return WalletTransaction::create([
                'user_id' => $locked->id,
                'reference' => $options['reference'] ?? ('WTX-'.strtoupper(Str::random(12))),
                'type' => Direction::DEBIT,
                'purpose' => $purpose,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $options['currency'] ?? 'NGN',
                'payment_method' => $options['payment_method'] ?? null,
                'status' => $options['status'] ?? Status::SUCCESS,
                'description' => $options['description'] ?? null,
                'meta' => $options['meta'] ?? null,
                'order_id' => $options['order_id'] ?? null,
            ]);
        });
    }
}
