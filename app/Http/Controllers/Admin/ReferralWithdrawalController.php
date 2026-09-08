<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralWithdrawal;
use App\Services\ReferralService;
use App\Services\WalletService;
use App\Types\TransactionType;
use App\Types\WithdrawalStatus;
use Illuminate\Http\Request;

class ReferralWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = ReferralWithdrawal::query()
            ->with('user:id,name,email')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('method'), fn ($q) => $q->where('method', $request->method))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.referral.withdrawals.index', ['withdrawals' => $withdrawals]);
    }

    public function show(ReferralWithdrawal $withdrawal)
    {
        return view('admin.referral.withdrawals.show', ['withdrawal' => $withdrawal->load('user')]);
    }

    /** For method=bank — marks as paid after an external bank transfer has been sent manually. */
    public function approveBank(Request $request, ReferralWithdrawal $withdrawal)
    {
        abort_if($withdrawal->status === WithdrawalStatus::SUCCESS, 422, 'Already processed.');

        $withdrawal->update([
            'status' => WithdrawalStatus::SUCCESS,
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Withdrawal marked as paid.');
    }

    /** For method=wallet — credits the user's spendable wallet balance directly. */
    public function approveWallet(Request $request, ReferralWithdrawal $withdrawal, WalletService $wallet)
    {
        abort_if($withdrawal->status === WithdrawalStatus::SUCCESS, 422, 'Already processed.');

        $wallet->credit($withdrawal->user, (float) $withdrawal->amount, TransactionType::REFERRAL_BONUS, [
            'currency' => $withdrawal->currency,
            'description' => "Referral withdrawal #{$withdrawal->id} — converted to wallet balance",
        ]);

        $withdrawal->update([
            'status' => WithdrawalStatus::SUCCESS,
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Wallet credited.');
    }

    public function reject(Request $request, ReferralWithdrawal $withdrawal, ReferralService $referralService)
    {
        $data = $request->validate(['failure_reason' => ['required', 'string', 'max:255']]);

        if ($withdrawal->status !== WithdrawalStatus::FAILED) {
            $referralService->credit($withdrawal->user, (float) $withdrawal->amount, "Withdrawal #{$withdrawal->id} rejected — funds returned");
        }

        $withdrawal->update([
            'status' => WithdrawalStatus::FAILED,
            'failure_reason' => $data['failure_reason'],
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Withdrawal rejected — funds returned.');
    }
}
