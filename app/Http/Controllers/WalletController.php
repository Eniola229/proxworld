<?php

namespace App\Http\Controllers;

use App\Services\BachsService;
use App\Services\CurrencyService;
use App\Services\FlutterwaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index(Request $request, CurrencyService $currencies)
    {
        $user = $request->user();
        $currency = $currencies->walletCurrency($user);

        return view('wallet.index', [
            'balance'      => $user->balance,
            'currency'     => $currency,
            'minTopUp'     => $currencies->minTopUp($currency),   // 500 for NGN, 5 for any other
            'transactions' => $user->wallet()->latest()->paginate(15),
        ]);
    }
 
    public function fund(Request $request, CurrencyService $currencies)
    {
        $currency = $currencies->walletCurrency($request->user());

        $data = $request->validate([
            'amount'   => ['required', 'numeric', 'min:' . $currencies->minTopUp($currency)],
            'provider' => ['nullable', 'in:flutterwave,bachs'],
        ], [
            'amount.min' => 'Minimum top-up is ' . $currencies->format($currencies->minTopUp($currency), $currency) . '.',
        ]);

        if ($currency === 'NGN') {
            return $this->fundWithVirtualAccount($request, (float) $data['amount']);
        }

        return ($data['provider'] ?? 'flutterwave') === 'bachs'
            ? $this->fundWithBachs($request, $currencies, $currency, (float) $data['amount'])
            : $this->fundWithCheckout($request, $currencies, $currency, (float) $data['amount']);
    }

    protected function fundWithVirtualAccount(Request $request, float $amount)
    {
        $user = $request->user();
        $txRef = 'PXW-' . strtoupper(Str::random(16));

        try {
            $account = app(FlutterwaveService::class)->createVirtualAccount([
                'amount'    => $amount,
                'currency'  => 'NGN',
                'reference' => $txRef,
                'customer'  => ['email' => $user->email, 'name' => $user->name],
                'meta'      => ['user_id' => $user->id],
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            \Log::critical('Unhandled error in user payment fund:', [
                'error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
            return back()->with('error', 'An unexpected error occurred while starting payment.');
        }

        session(['pending_topup_reference' => $txRef]);

        return back()->with('virtualAccount', [
            'account_number'    => $account['account_number'] ?? null,
            'account_bank_name' => $account['account_bank_name'] ?? null,
            'amount'            => $account['amount'] ?? $amount,
            'reference'         => $txRef,
            'expires_at'        => $account['account_expiration_datetime'] ?? null,
            'note'              => $account['note'] ?? null,
        ]);
    }

    protected function fundWithCheckout(Request $request, CurrencyService $currencies, string $currency, float $amount)
    {
        $user = $request->user();
        $amount = $currencies->roundForCharge($amount, $currency);
        $txRef = 'PXC-' . strtoupper(Str::random(16));

        try {
            $link = app(FlutterwaveService::class)->createCheckoutLink([
                'amount'       => $amount,
                'currency'     => $currency,
                'reference'    => $txRef,
                'redirect_url' => route('flutterwave.checkout-return'),
                'customer'     => ['email' => $user->email, 'name' => $user->name],
                'meta'         => ['user_id' => $user->id],
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('alert', ['type' => 'error', 'message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            \Log::critical('Unhandled error starting Flutterwave checkout:', [
                'error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
            return back()->with('alert', ['type' => 'error', 'message' => 'An unexpected error occurred while starting payment.']);
        }

        return redirect()->away($link);
    }

    protected function fundWithBachs(Request $request, CurrencyService $currencies, string $currency, float $amount)
    {
        if (! BachsService::supports($currency)) {
            return back()->with('alert', ['type' => 'error', 'message' => "Bachs does not support {$currency} yet. Please pay with Flutterwave."]);
        }

        $user = $request->user();
        $amount = $currencies->roundForCharge($amount, $currency);
        $txRef = 'PXB-' . strtoupper(Str::random(16));

        try {
            $checkout = app(BachsService::class)->createCheckout([
                'amount'      => $amount,
                'currency'    => $currency,
                'reference'   => $txRef,
                'success_url' => route('bachs.return', ['reference' => $txRef]),
                'cancel_url'  => route('bachs.cancelled'),
                'customer'    => ['email' => $user->email, 'name' => $user->name],
                'meta'        => ['user_id' => $user->id],
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('alert', ['type' => 'error', 'message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            \Log::critical('Unhandled error starting Bachs checkout:', [
                'error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
            return back()->with('alert', ['type' => 'error', 'message' => 'An unexpected error occurred while starting payment.']);
        }

        return redirect()->away($checkout['checkout_url']);
    }

    public function topupStatus(Request $request)
    {
        $reference = $request->query('reference');

        $log = $request->user()->wallet()->where('reference', $reference)->first();

        return response()->json(['status' => $log?->status ?? 'pending']);
    }
}