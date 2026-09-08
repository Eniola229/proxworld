<?php

namespace App\Types;

class WalletTransactionStatus
{
    const PENDING = 'pending';
    const SUCCESS = 'success';
    const FAILED = 'failed';

    public static function all(): array
    {
        return [self::PENDING, self::SUCCESS, self::FAILED];
    }
}
