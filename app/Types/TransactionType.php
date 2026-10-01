<?php

namespace App\Types;

/** wallet_transactions.purpose */
class TransactionType
{
    const TOPUP = 'topup';
    const ORDER_DEBIT = 'order_debit';
    const ORDER_REFUND = 'order_refund';
    const ADMIN_CREDIT = 'admin_credit';
    const ADMIN_DEBIT = 'admin_debit';
    const REFERRAL_BONUS = 'referral_bonus';
    const CURRENCY_SWITCH = 'currency_switch';

    public static function all(): array
    {
        return [self::TOPUP, self::ORDER_DEBIT, self::ORDER_REFUND, self::ADMIN_CREDIT, self::ADMIN_DEBIT, self::REFERRAL_BONUS, self::CURRENCY_SWITCH];
    }
}