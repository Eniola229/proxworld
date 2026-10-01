<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Types\TransactionType;
use App\Types\WalletTransactionDirection as Direction;
use App\Types\WalletTransactionStatus as Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class WalletService
{
    public function credit(User $user, float $amount, string $purpose, array $options = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be positive.');
        }

        return DB::transaction(function () use ($user, $amount, $purpose, $options) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $currency = $this->walletCurrency($locked);
            [$credited, $meta] = $this->intoWalletCurrency($amount, $options['currency'] ?? $currency, $currency, $options['meta'] ?? []);

            $before = (float) $locked->balance;
            $after = round($before + $credited, 4);

            $locked->forceFill(['balance' => $after])->save();

            return WalletTransaction::create([
                'user_id' => $locked->id,
                'reference' => $options['reference'] ?? ('WTX-'.strtoupper(Str::random(12))),
                'type' => Direction::CREDIT,
                'purpose' => $purpose,
                'amount' => $credited,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $currency,
                'payment_method' => $options['payment_method'] ?? null,
                'status' => $options['status'] ?? Status::SUCCESS,
                'description' => $options['description'] ?? null,
                'meta' => $meta ?: null,
                'order_id' => $options['order_id'] ?? null,
            ]);
        });
    }

    /** @throws InsufficientBalanceException */
    public function debit(User $user, float $amount, string $purpose, array $options = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be positive.');
        }

        return DB::transaction(function () use ($user, $amount, $purpose, $options) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $currency = $this->walletCurrency($locked);
            [$debited, $meta] = $this->intoWalletCurrency($amount, $options['currency'] ?? $currency, $currency, $options['meta'] ?? []);

            $before = (float) $locked->balance;

            if ($before < $debited) {
                throw new InsufficientBalanceException($locked, $debited, $before);
            }

            $after = round($before - $debited, 4);

            $locked->forceFill(['balance' => $after])->save();

            return WalletTransaction::create([
                'user_id' => $locked->id,
                'reference' => $options['reference'] ?? ('WTX-'.strtoupper(Str::random(12))),
                'type' => Direction::DEBIT,
                'purpose' => $purpose,
                'amount' => $debited,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $currency,
                'payment_method' => $options['payment_method'] ?? null,
                'status' => $options['status'] ?? Status::SUCCESS,
                'description' => $options['description'] ?? null,
                'meta' => $meta ?: null,
                'order_id' => $options['order_id'] ?? null,
            ]);
        });
    }

    /** Changes wallet currency and converts the balance at today's rate, atomically. referral_balance (NGN ledger) is untouched. */
    public function switchCurrency(User $user, string $to): ?WalletTransaction
    {
        $to = strtoupper($to);

        return DB::transaction(function () use ($user, $to) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            $from = $this->walletCurrency($locked);

            if ($from === $to) {
                return null;
            }

            $rate = app(ExchangeRateService::class)->rateOrNull($from, $to);

            if ($rate === null || $rate <= 0) {
                throw new RuntimeException("We couldn't get an exchange rate for {$from} → {$to} right now. Please try again shortly.");
            }

            $before = (float) $locked->balance;
            $after = round($before * $rate, 4);

            $locked->forceFill(['balance' => $after, 'preferred_currency' => $to])->save();

            if ($before <= 0) {
                return null;
            }

            return WalletTransaction::create([
                'user_id' => $locked->id,
                'reference' => 'CSW-'.strtoupper(Str::random(12)),
                'type' => Direction::CREDIT,
                'purpose' => TransactionType::CURRENCY_SWITCH,
                'amount' => $after,
                'balance_before' => $before,
                'balance_after' => $after,
                'currency' => $to,
                'status' => Status::SUCCESS,
                'description' => "Wallet currency switched {$from} → {$to}",
                'meta' => ['from_currency' => $from, 'to_currency' => $to, 'rate' => $rate],
            ]);
        });
    }

    protected function walletCurrency(User $user): string
    {
        return strtoupper($user->preferred_currency ?: 'NGN');
    }

    protected function intoWalletCurrency(float $amount, string $from, string $walletCurrency, array $meta): array
    {
        $from = strtoupper($from);

        if ($from === $walletCurrency) {
            return [$amount, $meta];
        }

        $rate = app(ExchangeRateService::class)->rateOrNull($from, $walletCurrency);

        if ($rate === null) {
            throw new RuntimeException("No exchange rate available for {$from} → {$walletCurrency}.");
        }

        $converted = round($amount * $rate, 4);

        if ($converted <= 0) {
            throw new RuntimeException('Amount is too small after currency conversion.');
        }

        return [$converted, array_merge($meta, ['converted_from' => ['amount' => $amount, 'currency' => $from, 'rate' => $rate]])];
    }
}