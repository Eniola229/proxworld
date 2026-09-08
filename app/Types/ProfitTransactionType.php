<?php

namespace App\Types;

/** profit_transactions.type — the reseller's separate, withdrawable earnings ledger */
class ProfitTransactionType
{
    const ORDER_MARKUP = 'order_markup';
    const WITHDRAWAL_REQUEST = 'withdrawal_request';
    const WITHDRAWAL_REJECTED_REFUND = 'withdrawal_rejected_refund';
    const ADMIN_ADJUSTMENT = 'admin_adjustment';

    public static function all(): array
    {
        return [self::ORDER_MARKUP, self::WITHDRAWAL_REQUEST, self::WITHDRAWAL_REJECTED_REFUND, self::ADMIN_ADJUSTMENT];
    }
}
