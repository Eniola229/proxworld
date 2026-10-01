<?php

namespace App\Console\Commands;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Provider;
use App\Models\User;
use App\Services\CurrencyService;
use App\Services\ExchangeRateService;
use App\Services\InsufficientBalanceException;
use App\Services\WalletService;
use App\Types\OrderChannel;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

/**
 * Local testing only: creates a COMPLETED order without calling any provider,
 * debits the user's wallet for real (in their wallet currency), and sends/previews
 * the order confirmation email.
 *
 *   php artisan orders:mock you@example.com
 *   php artisan orders:mock you@example.com --ngn=5000 --mail=preview
 *   php artisan orders:mock you@example.com --cleanup      (refunds + deletes the mock orders)
 */
class MockOrder extends Command
{
    protected $signature = 'orders:mock
                            {email : Email of the user to charge}
                            {--ngn=2500 : Order price expressed in NGN (converted to the user\'s wallet currency)}
                            {--qty=1 : Quantity}
                            {--mail=send : send | preview | none}
                            {--cleanup : Refund and delete this user\'s mock orders instead of creating one}
                            {--force : Allow running in production}';

    protected $description = 'Create a mock completed order that debits the wallet and sends the confirmation email (no provider call)';

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
            return $this->cleanup($user, $currency, $wallet);
        }

        $ngn = max(1.0, (float) $this->option('ngn'));
        $qty = max(1, (int) $this->option('qty'));

        $rate = $rates->rateOrNull('NGN', $currency);

        if ($rate === null) {
            $this->error("No exchange rate for NGN -> {$currency}. Run: php artisan exchange-rates:sync");

            return self::FAILURE;
        }

        $charge = $currencies->roundForCharge($ngn * $rate, $currency);
        $cost = round($ngn * 0.8, 4); // pretend 20% margin, all in NGN like real orders

        try {
            $walletTx = $wallet->debit($user, $charge, TransactionType::ORDER_DEBIT, [
                'currency' => $currency,
                'description' => '[MOCK] Order: Mock ISP Proxy x'.$qty,
            ]);
        } catch (InsufficientBalanceException $e) {
            $this->error('Insufficient balance. Top up this wallet first (or lower --ngn).');

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
            'markup_percentage' => 25,
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

        $this->info("Order {$order->id} created and wallet debited {$currencies->format($charge, $currency)}.");
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

    private function cleanup(User $user, string $currency, WalletService $wallet): int
    {
        $orders = Order::where('user_id', $user->id)->where('admin_note', self::MARKER)->get();

        if ($orders->isEmpty()) {
            $this->info('No mock orders found for this user.');

            return self::SUCCESS;
        }

        foreach ($orders as $order) {
            $wallet->credit($user, (float) $order->charge, TransactionType::ORDER_REFUND, [
                'currency' => $order->currency,
                'description' => "[MOCK] Refund for mock order {$order->id}",
            ]);
            $order->delete();
        }

        $this->info($orders->count().' mock order(s) refunded and deleted.');

        return self::SUCCESS;
    }
}