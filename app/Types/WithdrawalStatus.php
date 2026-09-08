<?php

namespace App\Types;

/** Used for both reseller-profit withdrawals and referral-earnings withdrawals. */
class WithdrawalStatus
{
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const SUCCESS = 'success';
    const FAILED = 'failed';

    public static function all(): array
    {
        return [self::PENDING, self::PROCESSING, self::SUCCESS, self::FAILED];
    }
}
