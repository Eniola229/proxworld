<?php

namespace App\Console\Commands;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderExtension;
use App\Models\Provider;
use App\Models\ProfitTransaction;
use App\Models\User;
use App\Services\CurrencyService;
use App\Services\ExchangeRateService;
use App\Services\InsufficientBalanceException;
use App\Services\ProfitService;
use App\Services\WalletService;
use App\Types\OrderChannel;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

/**
 * Local testing only: creates a COMPLETED order without calling any provider,
 * debits the user's wallet for real (priced in their wallet currency), writes the
 * profit ledger row, and sends/previews the order confirmation email.
 *
 *   php artisan orders:mock you@example.com --amount=10 --mail=none
 *   php artisan orders:mock you@example.com --amount=10 --margin=30 --mail=preview
 *   php artisan orders:mock you@example.com --cleanup   (refunds + deletes mock orders AND their extensions)
 */
class MockOrder extends Command
{
    protected $signature = 'orders:mock
                            {email : Email of the user to charge}
                            {--amount=10 : Order price in the USER\'S WALLET CURRENCY}
                            {--margin=20 : Profit margin % of the sell price}
                            {--qty=1 : Quantity}
                            {--mail=send : send | preview | none}
                            {--cleanup : Refund and delete this user\'s mock orders (and their extensions) instead of creating one}
                            {--force : Allow running in production}';

    protected $description = 'Create a mock completed order that debits the wallet and writes the profit ledger (no provider call)';

    private const MARKER = 'MOCK TEST ORDER';

    public function handle(CurrencyService $currencies, ExchangeRateService $rates, WalletService $wallet): int
    {
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('Refusing to run in production. Use --force if you really mean it.');

            return self::FAILURE;
        }

        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user with that email.');

            return self::FAILURE;
        }

        $currency = $currencies->walletCurrency($user);

        if ($this->option('cleanup')) {
            return $this->cleanup($user, $wallet);
        }

        $qty = max(1, (int) $this->option('qty'));
        $margin = min(95, max(0, (float) $this->option('margin')));

        $rate = $rates->rateOrNull('NGN', $currency);

        if ($rate === null) {
            $this->error("No exchange rate for NGN -> {$currency}. Run: php artisan exchange-rates:sync");

            return self::FAILURE;
        }

        $charge = $currencies->roundForCharge((float) $this->option('amount'), $currency);
        $ngn = $rates->convert($charge, $currency, 'NGN');   // NGN base derived from what the user paid
        $cost = round($ngn * (1 - $margin / 100), 4);

        try {
            $walletTx = $wallet->debit($user, $charge, TransactionType::ORDER_DEBIT, [
                'currency' => $currency,
                'description' => '[MOCK] Order: Mock ISP Proxy x'.$qty,
            ]);
        } catch (InsufficientBalanceException $e) {
            $this->error('Insufficient balance. Top up this wallet first (or lower --amount).');

            return self::FAILURE;
        }

        $order = Order::create([
            'user_id' => $user->id,
            'provider_id' => Provider::query()->value('id'),
            'external_service_id' => 'mock-service',
            'api_order_id' => 'MOCK-'.strtoupper(substr(md5((string) microtime(true)), 0, 8)),
            'service_name' => 'Mock ISP Proxy',
            'product_type' => 'isp',
            'quantity' => $qty,
            'cost_price_snapshot' => $cost,
            'platform_price_snapshot' => $ngn,
            'charge' => $charge,
            'currency' => $currency,
            'exchange_rate_snapshot' => $rate,
            'markup_percentage' => round($margin / (100 - $margin) * 100, 4),
            'profit' => round($ngn - $cost, 4),
            'channel' => OrderChannel::DIRECT,
            'status' => OrderStatus::COMPLETED,
            'admin_note' => self::MARKER,
            'proxy_data' => [[
                'ip' => '203.0.113.10',
                'port' => '8080',
                'username' => 'mockuser',
                'password' => 'mockpass',
            ]],
            'proxy_synced_at' => now(),
            'provider_synced_at' => now(),
        ]);

        $walletTx->update(['order_id' => $order->id]);

        app(ProfitService::class)->recordForOrder($order->fresh());

        $this->info("Order {$order->id} created and wallet debited {$currencies->format($charge, $currency)}.");
        $this->line('Sell (NGN): '.number_format($ngn, 2).' | Cost (NGN): '.number_format($cost, 2).' | Profit (NGN): '.number_format((float) $order->profit, 2));
        $this->line('New balance: '.$currencies->format((float) $user->fresh()->balance, $currency));

        return $this->handleMail($order->fresh('user'));
    }

    private function handleMail(Order $order): int
    {
        $mode = $this->option('mail');

        if ($mode === 'preview') {
            $path = storage_path('app/mock-order-email.html');
            File::put($path, view('emails.orders.confirmed', ['order' => $order])->render());
            $this->info("Email preview written to: {$path}  (open it in your browser)");
        } elseif ($mode === 'send') {
            try {
                // sendNow: OrderConfirmationMail implements ShouldQueue, so plain send() would wait for a queue worker.
                Mail::to($order->user->email)->sendNow(new OrderConfirmationMail($order));
                $this->info("Email sent to {$order->user->email} (if MAIL_MAILER=log, check storage/logs/laravel.log).");
            } catch (\Throwable $e) {
                $this->error('Email failed: '.$e->getMessage());

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    private function cleanup(User $user, WalletService $wallet): int
    {
        $orders = Order::where('user_id', $user->id)->where('admin_note', self::MARKER)->get();

        if ($orders->isEmpty()) {
            $this->info('No mock orders found for this user.');

            return self::SUCCESS;
        }

        $extCount = 0;

        foreach ($orders as $order) {
            // Refund every extension that was charged and not already refunded (failed ones were)
            foreach (OrderExtension::where('order_id', $order->id)->where('status', '!=', 'failed')->get() as $ext) {
                $wallet->credit($user, (float) $ext->charge, TransactionType::ORDER_REFUND, [
                    'currency' => $ext->currency,
                    'description' => "[MOCK] Refund for mock extension {$ext->id}",
                ]);
                $extCount++;
            }

            ProfitTransaction::where('order_id', $order->id)->delete();
            OrderExtension::where('order_id', $order->id)->delete();

            $wallet->credit($user, (float) $order->charge, TransactionType::ORDER_REFUND, [
                'currency' => $order->currency,
                'description' => "[MOCK] Refund for mock order {$order->id}",
            ]);
            $order->delete();
        }

        $this->info($orders->count()." mock order(s) and {$extCount} extension(s) refunded and deleted.");

        return self::SUCCESS;
    }
}