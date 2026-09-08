<?php

namespace App\Http\Controllers;

use App\Models\ReferralWithdrawal;
use App\Services\ReferralService;
use App\Services\WalletService;
use App\Types\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('referral.index', [
            'referral' => $user, // ->referral_code, ->referral_balance read directly off the User
            'referrals' => $user->referrals()->with('referredUser:id,name,created_at')->latest()->get(),
            'shareUrl' => route('referral.capture', $user->referral_code),
        ]);
    }

    public function withdrawForm(Request $request)
    {
        return view('referral.withdraw', ['availableBalance' => $request->user()->referral_balance]);
    }

    /** Withdraw to a bank account — goes through admin review, same as reseller withdrawals. */
    public function withdrawBank(Request $request, ReferralService $referralService)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:500'],
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:20'],
            'account_name' => ['required', 'string', 'max:100'],
        ]);

        $user = $request->user();

        try {
            // Reserve the funds immediately to prevent double-withdrawal requests.
            $referralService->debit($user, $data['amount'], 'Withdrawal request (bank transfer)');
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Insufficient referral balance.');
        }

        ReferralWithdrawal::create([
            'user_id' => $user->id,
            'method' => 'bank',
            'amount' => $data['amount'],
            'currency' => $user->preferred_currency,
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'reference' => 'RWD-'.strtoupper(Str::random(12)),
            'status' => 'pending',
        ]);

        return redirect()->route('referral.index')->with('success', 'Withdrawal request submitted — funds have been reserved pending review.');
    }

    /** Convert referral earnings to wallet balance — still goes through admin review, same as bank withdrawals. */
    public function withdrawToWallet(Request $request, ReferralService $referralService)
    {
        $data = $request->validate(['amount' => ['required', 'numeric', 'min:100']]);
        $user = $request->user();

        try {
            $referralService->debit($user, $data['amount'], 'Withdrawal request (to wallet)');
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Insufficient referral balance.');
        }

        ReferralWithdrawal::create([
            'user_id' => $user->id,
            'method' => 'wallet',
            'amount' => $data['amount'],
            'currency' => $user->preferred_currency,
            'reference' => 'RWD-'.strtoupper(Str::random(12)),
            'status' => 'pending',
        ]);

        return redirect()->route('referral.index')->with('success', 'Request submitted — your wallet will be credited once approved.');
    }
}
