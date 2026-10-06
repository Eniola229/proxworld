<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderExtension;
use App\Models\ProfitTransaction;
use App\Types\OrderChannel;

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
            'currency' => 'NGN', // CHANGED from $order->currency, see the bug note at the bottom
        ]);
    }

    public function recordForExtension(OrderExtension $extension): ?ProfitTransaction
    {
        if ($extension->status !== 'completed' || $extension->profit === null) {
            return null;
        }

        return ProfitTransaction::firstOrCreate(
            ['order_extension_id' => $extension->id],
            [
                'order_id' => $extension->order_id,
                'provider_id' => $extension->provider_id,
                'reseller_id' => null,
                'channel' => OrderChannel::DIRECT,
                'amount' => $extension->profit,
                'currency' => 'NGN',
            ]
        );
    }
}