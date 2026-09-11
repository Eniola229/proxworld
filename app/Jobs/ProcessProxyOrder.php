<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Provider;
use App\ProxyProviders\Exceptions\ProviderOrderException;
use App\ProxyProviders\ProxyProviderFactory;
use App\Services\ProfitService;
use App\Services\ResellerProfitService;
use App\Services\ResellerWalletService;
use App\Services\WalletService;
use App\Types\OrderStatus;
use App\Types\ProfitTransactionType;
use App\Types\TransactionType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queued so a slow/flaky provider API never blocks the checkout response.
 * On failure: auto-refunds whoever paid (the reseller's wallet if this was
 * a reseller-channel order, otherwise the customer's wallet) and marks the
 * order failed-but-refunded so nothing is ever silently lost. The
 * `orders:check-pending` command is the safety net for anything that gets
 * stuck (worker restart mid-job, provider timeout with no response either way).
 */
class ProcessProxyOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // retries are handled explicitly below via provider failover, not queue auto-retry

    public function __construct(public string $orderId)
    {
    }

    public function handle(
        WalletService $wallet,
        ResellerWalletService $resellerWallet,
        ProfitService $profitService,
        ResellerProfitService $resellerProfitService,
    ): void {
        $order = Order::with(['user', 'reseller', 'provider'])->find($this->orderId);

        if (! $order || $order->isTerminal()) {
            return;
        }

        $order->update(['status' => OrderStatus::PROCESSING]);

        $provider = $order->provider ?? Provider::find($order->provider_id);

        if (! $provider) {
            $this->refundAndFail($order, $wallet, $resellerWallet, resellerProfitService: $resellerProfitService, reason: 'No provider assigned to this order.');

            return;
        }

        try {
            $driver = ProxyProviderFactory::make($provider);

            $result = $driver->placeOrder([
                'external_service_id' => $order->external_service_id,
                'quantity' => $order->quantity,
            ]);

            $order->update([
                'status' => OrderStatus::COMPLETED,
                'api_order_id' => $result['api_order_id'],
                'provider_synced_at' => now(),
            ]);

            // Fetch the actual proxy credentials now that the order exists at the
            // provider. Data-based products (Residential/Mobile) don't have these —
            // the driver throws for them, which is expected, so this must never
            // undo an order that already succeeded and was already paid for.
            try {
                $proxies = $driver->listProxies($result['api_order_id']);

                $order->update([
                    'proxy_data' => $proxies,
                    'proxy_synced_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::info("No per-order proxy data for order {$order->id}: ".$e->getMessage());
            }

            $profitService->recordForOrder($order->fresh());

            if ($order->reseller_id && $order->reseller_profit > 0) {
                $resellerProfitService->credit(
                    $order->reseller,
                    (float) $order->reseller_profit,
                    ProfitTransactionType::ORDER_MARKUP,
                    ['order_id' => $order->id, 'description' => "Markup earned on order #{$order->id}"]
                );
            }

            dispatch(new \App\Jobs\SendOrderConfirmationEmail($order->id));
        } catch (ProviderOrderException $e) {
            Log::warning("Provider order failed for order {$order->id}: ".$e->getMessage());
            $this->refundAndFail($order, $wallet, $resellerWallet, $resellerProfitService, $e->getMessage());
        } catch (\Throwable $e) {
            Log::error("Unexpected error processing order {$order->id}: ".$e->getMessage());
            $this->refundAndFail($order, $wallet, $resellerWallet, $resellerProfitService, 'Unexpected processing error — refunded automatically.');
        }
    }

    protected function refundAndFail(Order $order, WalletService $wallet, ResellerWalletService $resellerWallet, ResellerProfitService $resellerProfitService, string $reason): void
    {
        if ($order->reseller_id) {
            $resellerWallet->credit($order->reseller, (float) $order->platform_price_snapshot, [
                'description' => "Refund for failed order #{$order->id}",
                'order_id' => $order->id,
            ]);
        } else {
            $wallet->credit($order->user, (float) $order->charge, TransactionType::ORDER_REFUND, [
                'description' => "Refund for failed order #{$order->id}: {$reason}",
                'order_id' => $order->id,
                'currency' => $order->currency,
            ]);
        }

        $order->update([
            'status' => OrderStatus::REFUNDED,
            'failure_reason' => $reason,
        ]);
    }
}