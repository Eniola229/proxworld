<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use RuntimeException;

class CurrencyController extends Controller
{
    public function switch(Request $request, CurrencyService $currencies, WalletService $wallet)
    {
        $data = $request->validate(['currency' => ['required', 'string', 'size:3']]);

        $to = strtoupper($data['currency']);

        if (! $currencies->isSupported($to)) {
            return back()->with('alert', ['type' => 'error', 'message' => 'That currency is not available.']);
        }

        try {
            $wallet->switchCurrency($request->user(), $to);
        } catch (RuntimeException $e) {
            return back()->with('alert', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        return back()->with('alert', [
            'type' => 'success',
            'message' => "Currency switched to {$to}. Your balance has been converted.",
        ]);
    }
}