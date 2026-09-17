<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Storefront customer's own wallet (User::balance) — NOT the reseller's
 * wholesale funding wallet (see App\Http\Controllers\Reseller\WalletController,
 * a completely different balance on the Reseller model). Mirrors
 * App\Http\Controllers\WalletController (main site) exactly; only the view,
 * session key, and the two URL variables below differ.
 */
class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('reseller.wallet.index', [
            'balance' => $user->balance,
            'transactions' => $user->wallet()->latest()->paginate(15),
            'currencies' => \App\Models\Currency::where('is_active', true)->get(),
            // dashboard/order/new views all had the same hardcoded route('reseller.*')
            'walletTopupUrl' => route('storefront.wallet.topup'),
            'walletTopupStatusUrl' => route('storefront.wallet.topup-status'),
        ]);
    }

    public function fund(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        $user = $request->user();
        $txRef = 'PXWS-' . strtoupper(Str::random(16));

        try {
            $account = app(\App\Services\FlutterwaveService::class)->createVirtualAccount([
                'amount' => (float) $data['amount'],
                'currency' => 'NGN',
                'reference' => $txRef,
                'customer' => [
                    'email' => $user->email,
                    'name' => $user->name,
                ],
                'meta' => ['user_id' => $user->id], // same meta shape as the main-site flow, so the shared Flutterwave webhook credits User::balance correctly
                'expiry' => 900,
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            \Log::critical('Unhandled error in storefront wallet fund:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'An unexpected error occurred while starting payment.');
        }

        session(['pending_topup_reference_storefront' => $txRef]);

        return back()->with('virtualAccount', [
            'account_number' => $account['account_number'] ?? null,
            'account_bank_name' => $account['account_bank_name'] ?? null,
            'amount' => $account['amount'] ?? $data['amount'],
            'reference' => $txRef,
            'expires_at' => $account['account_expiration_datetime'] ?? null,
            'note' => $account['note'] ?? null,
        ]);
    }

    public function topupStatus(Request $request)
    {
        $reference = $request->query('reference');

        $log = $request->user()->wallet()->where('reference', $reference)->first();

        return response()->json(['status' => $log?->status ?? 'pending']);
    }
}