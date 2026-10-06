<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderExtension;
use App\Models\ProfitTransaction;
use App\Models\User;
use App\Services\CurrencyService;
use App\Services\ExchangeRateService;
use App\Services\InsufficientBalanceException;
use App\Services\ProfitService;
use App\Services\WalletService;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Console\Command;

/**
 * Local testing only. Adds a mock EXTENSION to a mock order (creating the order if needed).
 * You enter the price in the USER'S WALLET CURRENCY (like a real checkout); the command
 * converts it to NGN to derive cost/revenue/profit, so you can verify the conversion.
 *
 *   php artisan orders:mock-extend you@example.com --amount=5            (5 in the user's currency)
 *   php artisan orders:mock-extend you@example.com --amount=5 --margin=30
 *   php artisan orders:mock-extend you@example.com --amount=5 --fail
 *   php artisan orders:mock-extend you@example.com --cleanup
 */
class MockExtension extends Command
{
    protected $signature = 'orders:mock-extend
                            {email : Email of the user to charge}
                            {--order= : Mock order ID to extend (defaults to the latest mock order, created if none)}
                            {--amount=5 : Extension price in the USER\'S WALLET CURRENCY}
                            {--order-amount= : Price of the auto-created mock order, in the wallet currency (optional)}
                            {--margin=20 : Profit margin % of the sell price}
                            {--qty=1 : Extension quantity}
                            {--fail : Simulate a provider failure (charged, then refunded, marked failed)}
                            {--cleanup : Refund and delete all mock orders + extensions for this user}
                            {--force : Allow running in production}';

    protected $description = 'Add a mock extension (priced in the wallet currency) and verify the NGN profit conversion';

    private const MARKER = 'MOCK TEST ORDER';

    public function handle(
        CurrencyService $currencies,
        ExchangeRateService $rates,
        WalletService $wallet,
        ProfitService $profits,
    ): int {
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('Refusing to run in production. Use --force if you really mean it.');

            return self::FAILURE;
        }

        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user with that email.');

            return self::FAILURE;
        }

        if ($this->option('cleanup')) {
            return $this->call('orders:mock', [
                'email' => $user->email,
                '--cleanup' => true,
                '--force' => (bool) $this->option('force'),
            ]);
        }

        $currency = $currencies->walletCurrency($user);

        $order = $this->option('order')
            ? Order::where('user_id', $user->id)->find($this->option('order'))
            : Order::where('user_id', $user->id)->where('admin_note', self::MARKER)->latest()->first();

        if (! $order && ! $this->option('order')) {
            $this->info('No mock order found, creating one first...');

            $args = ['email' => $user->email, '--mail' => 'none', '--force' => (bool) $this->option('force')];
            if ($this->option('order-amount') !== null) {
                $args['--amount'] = $this->option('order-amount');
            }

            if ($this->call('orders:mock', $args) !== self::SUCCESS) {
                return self::FAILURE;
            }

            $order = Order::where('user_id', $user->id)->where('admin_note', self::MARKER)->latest()->first();
        }

        if (! $order) {
            $this->error('Order not found for this user.');

            return self::FAILURE;
        }

        if ($order->status !== OrderStatus::COMPLETED) {
            $this->error("Order is {$order->status}, only completed orders can be extended.");

            return self::FAILURE;
        }

        $qty = max(1, (int) $this->option('qty'));
        $margin = min(95, max(0, (float) $this->option('margin')));
        $failing = (bool) $this->option('fail');

        // What the customer pays, in THEIR currency (this is the input)
        $charge = $currencies->roundForCharge((float) $this->option('amount'), $currency);

        // Convert to NGN base: this is the conversion under test
        $sellNgn = $rates->convert($charge, $currency, 'NGN');
        $costNgn = round($sellNgn * (1 - $margin / 100), 4);
        $profitNgn = round($sellNgn - $costNgn, 4);
        $markupPct = $margin < 100 ? round($margin / (100 - $margin) * 100, 4) : 0;
        $rate = $rates->rateOrNull('NGN', $currency);

        if ($rate === null) {
            $this->error("No exchange rate for NGN -> {$currency}. Run: php artisan exchange-rates:sync");

            return self::FAILURE;
        }

        try {
            $walletTx = $wallet->debit($user, $charge, TransactionType::ORDER_DEBIT, [
                'currency' => $currency,
                'description' => "[MOCK] Extension: {$order->service_name} x{$qty} (order #".substr($order->id, 0, 8).')',
                'order_id' => $order->id,
            ]);
        } catch (InsufficientBalanceException $e) {
            $this->error('Insufficient balance. Top up this wallet first (or lower --amount).');

            return self::FAILURE;
        }

        $extension = OrderExtension::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'provider_id' => $order->provider_id,
            'quantity' => $qty,
            'cost_price_snapshot' => $costNgn,
            'platform_price_snapshot' => $sellNgn,
            'profit' => $profitNgn,
            'markup_percentage' => $markupPct,
            'charge' => $charge,
            'currency' => $currency,
            'exchange_rate_snapshot' => $rate,
            'status' => 'pending',
            'wallet_transaction_id' => $walletTx->id ?? null,
        ]);

        if ($failing) {
            $wallet->credit($user, $charge, TransactionType::ORDER_REFUND, [
                'description' => "[MOCK] Refund for failed extension of order #{$order->id}",
                'order_id' => $order->id,
                'currency' => $currency,
            ]);

            $extension->update(['status' => 'failed', 'failure_reason' => 'Simulated provider failure (mock)']);

            $this->warn("Extension {$extension->id} FAILED (simulated). Wallet refunded {$currencies->format($charge, $currency)}.");
        } else {
            $extension->update(['status' => 'completed']);
            $ledger = $profits->recordForExtension($extension->fresh());

            $this->info("Extension {$extension->id} completed. Wallet debited {$currencies->format($charge, $currency)}.");
            $this->line('Ledger row: '.($ledger ? "{$ledger->id} (".number_format((float) $ledger->amount, 2)." {$ledger->currency})" : 'NOT WRITTEN'));
        }

        $this->line('New balance: '.$currencies->format((float) $user->fresh()->balance, $currency));

        $this->conversionCheck($currency, $charge, $sellNgn, $profitNgn, $margin, $rate, $rates, $extension->fresh());

        $order = $order->fresh();
        $this->newLine();
        $this->table(['Order', 'Original profit (NGN)', 'Completed ext.', 'Extension profit (NGN)', 'Lifetime profit (NGN)'], [[
            substr($order->id, 0, 8),
            number_format((float) $order->profit, 2),
            $order->extensions()->completed()->count(),
            number_format((float) $order->extensions()->completed()->sum('profit'), 2),
            number_format($order->total_profit, 2),
        ]]);

        $this->line('Ledger total for this order (NGN): '.number_format((float) ProfitTransaction::where('order_id', $order->id)->sum('amount'), 2));
        $this->line("Check: /admin/orders/{$order->id}  and  /orders/{$order->id}");

        return self::SUCCESS;
    }

    /** Independent re-calculation so you can eyeball that the NGN conversion is right. */
    private function conversionCheck(string $currency, float $charge, float $sellNgn, float $profitNgn, float $margin, float $rate, ExchangeRateService $rates, OrderExtension $ext): void
    {
        $this->newLine();
        $this->line('<options=bold>Conversion check</>');

        // Independent path: use the reverse rate (currency -> NGN) instead of convert()
        $reverse = $currency === 'NGN' ? 1.0 : $rates->rateOrNull($currency, 'NGN');
        $expectedSell = $reverse !== null ? $charge * $reverse : null;
        $expectedProfit = $expectedSell !== null ? $expectedSell * ($margin / 100) : null;

        $fmt = fn ($v) => $v === null ? 'n/a' : number_format((float) $v, 4);
        $diff = fn ($a, $b) => $b === null ? 'n/a' : number_format(abs($a - $b), 4);

        $this->table(['Item', 'Value'], [
            ['Customer paid', number_format($charge, 4)." {$currency}"],
            ["Rate NGN -> {$currency}", $fmt($rate)],
            ["Rate {$currency} -> NGN", $fmt($reverse)],
            ['Sell price stored (NGN)', $fmt($ext->platform_price_snapshot)],
            ['Sell price expected (NGN)', $fmt($expectedSell).'  (diff '.$diff((float) $ext->platform_price_snapshot, $expectedSell).')'],
            ['Profit stored (NGN)', $fmt($ext->profit)],
            ["Profit expected (NGN, {$margin}%)", $fmt($expectedProfit).'  (diff '.$diff((float) $ext->profit, $expectedProfit).')'],
            ["Profit back in {$currency} (stored x rate)", $fmt((float) $ext->profit * $rate)],
        ]);

        $this->line('Small diffs are normal when the two rates are not exact inverses of each other. A large diff means the conversion is wrong.');
    }
}