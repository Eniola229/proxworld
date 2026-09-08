<?php

namespace App\Console\Commands;

use App\Jobs\ProcessProxyOrder;
use App\Models\Order;
use App\Services\ResellerWalletService;
use App\Services\WalletService;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Console\Command;

/**
 * The safety net: catches orders stuck in pending/processing past a
 * timeout (worker restart mid-job, provider timeout with no response
 * either way) — retries once, then refunds and marks failed if it still
 * won't go through. Scheduled to run every few minutes.
 */
class CheckPendingOrders extends Command
{
    protected $signature = 'orders:check-pending {--timeout=10 : Minutes before a pending/processing order is considered stuck}';

    protected $description = 'Retry or refund orders stuck in pending/processing past the timeout window.';

    public function handle(WalletService $wallet, ResellerWalletService $resellerWallet): int
    {
        $timeout = (int) $this->option('timeout');

        $stuck = Order::whereIn('status', [OrderStatus::PENDING, OrderStatus::PROCESSING])
            ->where('updated_at', '<=', now()->subMinutes($timeout))
            ->get();

        if ($stuck->isEmpty()) {
            $this->info('No stuck orders found.');

            return self::SUCCESS;
        }

        foreach ($stuck as $order) {
            if ($order->retry_count < 2) {
                $this->warn("Retrying order {$order->id} (attempt ".($order->retry_count + 1).')');
                $order->increment('retry_count');
                $order->update(['status' => OrderStatus::PENDING]);
                ProcessProxyOrder::dispatch($order->id);

                continue;
            }

            $this->error("Order {$order->id} exceeded retry limit — refunding and marking failed.");

            if ($order->reseller_id) {
                $resellerWallet->credit($order->reseller, (float) $order->platform_price_snapshot, [
                    'description' => "Refund — order {$order->id} stuck and exceeded retries",
                    'order_id' => $order->id,
                ]);
            } else {
                $wallet->credit($order->user, (float) $order->charge, TransactionType::ORDER_REFUND, [
                    'description' => "Refund — order {$order->id} stuck and exceeded retries",
                    'order_id' => $order->id,
                    'currency' => $order->currency,
                ]);
            }

            $order->update([
                'status' => OrderStatus::REFUNDED,
                'failure_reason' => 'Exceeded retry limit while stuck in pending/processing.',
            ]);
        }

        $this->info("Processed {$stuck->count()} stuck order(s).");

        return self::SUCCESS;
    }
}
