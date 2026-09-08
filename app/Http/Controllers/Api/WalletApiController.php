<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletApiController extends Controller
{
    public function balance(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'balance' => (float) $user->balance,
            'currency' => $user->preferred_currency,
        ]);
    }
}
