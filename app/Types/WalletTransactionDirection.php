<?php

namespace App\Types;

/** wallet_transactions.type — the simple credit/debit direction shown in the UI. */
class WalletTransactionDirection
{
    const CREDIT = 'credit';
    const DEBIT = 'debit';

    public static function all(): array
    {
        return [self::CREDIT, self::DEBIT];
    }
}
