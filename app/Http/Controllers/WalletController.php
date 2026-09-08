<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('wallet.index', [
            'balance'      => $user->balance,
            'transactions' => $user->wallet()->latest()->paginate(15),
            'currencies'   => \App\Models\Currency::where('is_active', true)->get(),
        ]);
    }

    public function fund(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
        ]);

        $user = $request->user();
        $txRef = 'PXW-' . strtoupper(Str::random(16));

        try {
            $account = app(\App\Services\FlutterwaveService::class)->createVirtualAccount([
                'amount'   => (float) $data['amount'],
                'currency' => 'NGN',
                'reference' => $txRef,
                'customer' => [
                    'email' => $user->email,
                    'name'  => $user->name,
                ],
                'meta' => ['user_id' => $user->id],
                'expiry' => 900, // 15 minutes
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            \Log::critical('Unhandled error in user payment fund:', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            return back()->with('error', 'An unexpected error occurred while starting payment.');
        }

        // Store the reference so we can look up the eventual webhook-confirmed
        // status from the wallet page (e.g. via polling) without trusting
        // anything from the client.
        session(['pending_topup_reference' => $txRef]);

        return back()->with('virtualAccount', [
            'account_number'   => $account['account_number'] ?? null,
            'account_bank_name'=> $account['account_bank_name'] ?? null,
            'amount'           => $account['amount'] ?? $data['amount'],
            'reference'        => $txRef,
            'expires_at'       => $account['account_expiration_datetime'] ?? null,
            'note'             => $account['note'] ?? null,
        ]);
    }

    public function topupStatus(Request $request)
    {
        $reference = $request->query('reference');

        $log = $request->user()->wallet()->where('reference', $reference)->first();

        return response()->json([
            'status' => $log?->status ?? 'pending',
        ]);
    }
}