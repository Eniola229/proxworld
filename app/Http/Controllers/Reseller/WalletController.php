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
            'balance' => $reseller->balance,
            'transactions' => $reseller->wallet()->latest()->paginate(15),
        ]);
    }

    public function fund(Request $request)
    {
        $data = $request->validate(['amount' => ['required', 'numeric', 'min:100']]);

        $reseller = $request->user()->reseller;
        $txRef = 'PXWR-'.strtoupper(Str::random(16));

        $response = \Illuminate\Support\Facades\Http::withToken(config('services.flutterwave.secret_key'))
            ->post(config('services.flutterwave.base_url').'/payments', [
                'tx_ref' => $txRef,
                'amount' => $data['amount'],
                'currency' => 'NGN',
                'redirect_url' => route('reseller.wallet.index'),
                'customer' => ['email' => $request->user()->email, 'name' => $reseller->panel_name],
                'meta' => ['reseller_id' => $reseller->id],
            ]);

        if (! $response->successful()) {
            return back()->with('error', 'Could not start payment.');
        }

        return redirect()->away($response->json('data.link'));
    }
}
