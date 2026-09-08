<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $transactions = $user->wallet()->latest()->paginate(15);

        return view('wallet.index', [
            'balance' => $user->balance,
            'transactions' => $transactions,
            'currencies' => \App\Models\Currency::where('is_active', true)->get(),
        ]);
    }

    /** Initiates a Flutterwave checkout — does NOT credit the wallet directly (see FlutterwaveController). */
    public function fund(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'currency' => ['required', 'string', 'size:3'],
        ]);

        $user = $request->user();
        $txRef = 'PXW-'.strtoupper(Str::random(16));

        $response = \Illuminate\Support\Facades\Http::withToken(config('services.flutterwave.secret_key'))
            ->post(config('services.flutterwave.base_url').'/payments', [
                'tx_ref' => $txRef,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'redirect_url' => route('wallet.callback'),
                'customer' => ['email' => $user->email, 'name' => $user->name],
                'customizations' => ['title' => config('app.name').' Wallet Top-Up'],
                'meta' => ['user_id' => $user->id],
            ]);

        if (! $response->successful()) {
            return back()->with('error', 'Could not start payment. Please try again.');
        }

        return redirect()->away($response->json('data.link'));
    }
}
