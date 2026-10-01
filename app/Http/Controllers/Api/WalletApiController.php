<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class WalletApiController extends Controller
{
    public function balance(Request $request, CurrencyService $currencies)
    {
        $user = $request->user();

        return response()->json([
            'balance' => (float) $user->balance,
            'currency' => $currencies->walletCurrency($user),
        ]);
    }
}