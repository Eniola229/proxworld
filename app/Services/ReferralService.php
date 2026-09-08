<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Types\TransactionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * The ONLY code path allowed to change users.referral_balance. Separate
 * again from WalletService (spendable balance) and ResellerProfitService
 * (reseller markup earnings) — three distinct ledgers, three distinct
 * services, so none can accidentally cross-contaminate another's numbers.
 */
class ReferralService
{
    public function credit(User $user, float $amount, string $description, array $options = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be positive.');
        }

        return DB::transaction(function () use ($user, $amount, $description, $options) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $before = (float) $locked->referral_balance;
            $after = round($before + $amount, 4);
            $locked->forceFill(['referral_balance' => $after])->save();

            // Reused wallet_transactions table with purpose=referral_bonus keeps
            // one place to look for "everything that happened to this user
            // financially" while referral_balance itself stays fully separate
            // from the spendable `balance` column.
            return WalletTransaction::create([
                'user_id' => $locked->id,
                'reference' => $options['reference'] ?? ('RTX-'.strtoupper(Str::random(12))),
                'type' => 'credit',
                'purpose' => TransactionType::REFERRAL_BONUS,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $options['currency'] ?? 'NGN',
                'status' => 'success',
                'description' => $description,
                'meta' => array_merge(['ledger' => 'referral_balance'], $options['meta'] ?? []),
            ]);
        });
    }

    public function debit(User $user, float $amount, string $description, array $options = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be positive.');
        }

        return DB::transaction(function () use ($user, $amount, $description, $options) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $before = (float) $locked->referral_balance;

            if ($before < $amount) {
                throw new RuntimeException('Insufficient referral balance.');
            }

            $after = round($before - $amount, 4);
            $locked->forceFill(['referral_balance' => $after])->save();

            return WalletTransaction::create([
                'user_id' => $locked->id,
                'reference' => $options['reference'] ?? ('RTX-'.strtoupper(Str::random(12))),
                'type' => 'debit',
                'purpose' => TransactionType::REFERRAL_BONUS,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $options['currency'] ?? 'NGN',
                'status' => 'success',
                'description' => $description,
                'meta' => array_merge(['ledger' => 'referral_balance'], $options['meta'] ?? []),
            ]);
        });
    }

    /** Call this whenever a referred user deposits or places their first order. */
    public function checkAndPayBonus(User $referredUser): void
    {
        $referral = Referral::where('referred_user_id', $referredUser->id)->first();

        if (! $referral || $referral->bonus_paid) {
            return;
        }

        if ($referral->has_deposited && ! $referral->bonus_paid) {
            $this->credit(
                $referral->referrer,
                (float) $referral->bonus_amount,
                "Referral bonus for {$referredUser->name} funding their wallet"
            );

            $referral->update(['bonus_paid' => true, 'bonus_paid_at' => now()]);
        }
    }
}
