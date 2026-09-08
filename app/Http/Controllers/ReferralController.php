<?php

namespace App\Http\Controllers;

use App\Types\TransactionType;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('referral.index', [
            'referral' => $user,
            'totalReferred'  => $user->referrals()->count(),
            'depositedCount' => $user->referrals()->where('has_deposited', true)->count(),
            'bonusPaidCount' => $user->referrals()->where('bonus_paid', true)->count(),
            'referredUsers' => $user->referrals()
                ->with('referredUser:id,name,email,created_at')
                ->latest()
                ->paginate(10, ['*'], 'referrals_page'),
            'transactions' => $user->wallet()
                ->where('purpose', TransactionType::REFERRAL_BONUS)
                ->latest()
                ->paginate(10, ['*'], 'transactions_page'),
        ]);
    }
}