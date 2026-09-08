<?php

namespace App\Http\Controllers;

use App\Models\ReferralWithdrawal;
use App\Services\FlutterwaveService;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;

class ReferralWithdrawalController extends Controller
{
    public function __construct(protected FlutterwaveService $flutterwave)
    {
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $recentWithdrawals = ReferralWithdrawal::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('referral.withdraw', [
            'referral' => $user, // referral_balance is a column on User
            'banks' => $this->flutterwave->getBanks(),
            'recentWithdrawals' => $recentWithdrawals,
        ]);
    }

    public function resolveAccount(Request $request)
    {
        $data = $request->validate([
            'bank_name' => ['required', 'string'],
            'account_number' => ['required', 'digits:10'],
        ]);

        $bank = collect($this->flutterwave->getBanks())
            ->first(fn ($b) => $b['name'] === $data['bank_name']);

        if (! $bank) {
            return response()->json(['success' => false, 'message' => 'Unknown bank selected.'], 422);
        }

        try {
            $resolved = $this->flutterwave->resolveAccount($data['account_number'], (string) $bank['code']);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'account_name' => $resolved['account_name'] ?? null,
            'bank_code' => $bank['code'],
        ]);
    }

    public function withdrawToWallet(Request $request, ReferralService $referralService)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
        ]);

        $user = $request->user();
        $balanceBefore = (float) $user->referral_balance;

        try {
            $referralService->debit($user, (float) $data['amount'], 'Referral withdrawal to wallet requested');
        } catch (RuntimeException $e) {
            return back()->with('alert', ['type' => 'error', 'message' => 'Insufficient referral balance.']);
        }

        ReferralWithdrawal::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'currency' => 'NGN',
            'method' => 'wallet',
            'reference' => 'RFW-'.strtoupper(Str::random(12)),
            'status' => 'pending',
            'description' => 'Referral withdrawal to wallet',
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceBefore - (float) $data['amount'],
        ]);

        return back()->with('alert', ['type' => 'success', 'message' => 'Withdrawal request submitted for review.']);
    }

    public function withdrawToBank(Request $request, ReferralService $referralService)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['required', 'string', 'max:20'],
            'account_number' => ['required', 'digits:10'],
            'account_name' => ['required', 'string', 'max:100'],
        ]);

        $user = $request->user();
        $balanceBefore = (float) $user->referral_balance;

        try {
            $referralService->debit($user, (float) $data['amount'], 'Referral withdrawal to bank requested');
        } catch (RuntimeException $e) {
            return back()->with('alert', ['type' => 'error', 'message' => 'Insufficient referral balance.']);
        }

        ReferralWithdrawal::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'currency' => 'NGN',
            'method' => 'bank',
            'bank_name' => $data['bank_name'],
            'bank_code' => $data['bank_code'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'reference' => 'RFW-'.strtoupper(Str::random(12)),
            'status' => 'pending',
            'description' => 'Referral withdrawal to bank',
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceBefore - (float) $data['amount'],
        ]);

        return back()->with('alert', ['type' => 'success', 'message' => 'Withdrawal request submitted for review.']);
    }
}