<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProfitTransaction;

/**
 * Platform-level profit ledger — separate from any wallet. Purely additive
 * reporting data; never affects anyone's spendable balance. Only ever
 * written when an order is marked completed (see App\Jobs\ProcessProxyOrder).
 */
class ProfitService
{
    public function recordForOrder(Order $order): ?ProfitTransaction
    {
        if ($order->profit === null) {
            return null;
        }

        return ProfitTransaction::create([
            'order_id' => $order->id,
            'provider_id' => $order->provider_id,
            'reseller_id' => $order->reseller_id,
            'channel' => $order->channel,
            'amount' => $order->profit,
            'currency' => $order->currency,
        ]);
    }
}
