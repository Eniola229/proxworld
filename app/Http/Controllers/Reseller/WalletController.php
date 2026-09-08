<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $reseller = $request->user()->reseller;

        return view('reseller.wallet.index', [
            'balance'      => $reseller?->balance ?? 0,
            'transactions' => $reseller ? $reseller->wallet()->latest()->paginate(15) : collect(),
        ]);
    }

    public function fund(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        $user = $request->user();
        $reseller = $user->reseller;

        if (! $reseller) {
            return back()->with('error', 'Reseller profile not found for this account.');
        }

        $txRef = 'PXWR-' . strtoupper(Str::random(16));

        try {
            $account = app(\App\Services\FlutterwaveService::class)->createVirtualAccount([
                'amount'   => (float) $data['amount'],
                'currency' => 'NGN',
                'reference' => $txRef,
                'customer' => [
                    'email' => $user->email,
                    'name'  => $reseller->panel_name ?? $user->name,
                ],
                'meta'    => ['reseller_id' => $reseller->id],
                'expiry'  => 900,
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            \Log::critical('Unhandled error in reseller payment fund:', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            return back()->with('error', 'An unexpected error occurred while starting payment.');
        }

        session(['pending_topup_reference_reseller' => $txRef]);

        return back()->with('virtualAccount', [
            'account_number'    => $account['account_number'] ?? null,
            'account_bank_name' => $account['account_bank_name'] ?? null,
            'amount'            => $account['amount'] ?? $data['amount'],
            'reference'         => $txRef,
        ]);
    }

    public function topupStatus(Request $request)
    {
        $reference = $request->query('reference');
        $reseller = $request->user()->reseller;

        $tx = $reseller ? \App\Models\ResellerWalletTransaction::where('reseller_id', $reseller->id)
            ->where('reference', $reference)->first() : null;

        return response()->json(['status' => $tx?->status ?? 'pending']);
    }
}