<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLogged;
use App\Models\ReferralWithdrawal;
use App\Services\FlutterwaveService;
use App\Services\ReferralService;
use App\Services\WalletService;
use App\Types\TransactionType;
use App\Types\WithdrawalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ReferralWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');
        $filterMethod = $request->get('method');
        $amountMin = $request->get('amount_min');
        $amountMax = $request->get('amount_max');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $stats = [
            'pending' => ReferralWithdrawal::where('status', WithdrawalStatus::PENDING)->count(),
            'processing' => ReferralWithdrawal::where('status', WithdrawalStatus::PROCESSING)->count(),
            'success' => ReferralWithdrawal::where('status', WithdrawalStatus::SUCCESS)->count(),
            'failed' => ReferralWithdrawal::where('status', WithdrawalStatus::FAILED)->count(),
        ];

        $withdrawals = ReferralWithdrawal::query()
            ->with('user:id,name,email')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($filterMethod, fn ($q) => $q->where('method', $filterMethod))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($amountMin, fn ($q) => $q->where('amount', '>=', $amountMin))
            ->when($amountMax, fn ($q) => $q->where('amount', '<=', $amountMax))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.referral.withdrawals.index', compact(
            'withdrawals', 'stats', 'status', 'search', 'filterMethod',
            'amountMin', 'amountMax', 'dateFrom', 'dateTo'
        ));
    }

    public function show(ReferralWithdrawal $withdrawal)
    {
        $withdrawal->load('user', 'processedBy');

        $referralBalance = $withdrawal->user->referral_balance;

        $totalEarnings = $withdrawal->user->referrals()->sum('bonus_amount');

        $totalTransactions = ReferralWithdrawal::where('user_id', $withdrawal->user_id)
            ->where('status', WithdrawalStatus::SUCCESS)
            ->count();

        // Combined admin-action log + this withdrawal's own record, newest first.
        // AdminLogged entries scoped to this withdrawal; the withdrawal's own
        // lifecycle (created/processed) is folded in as a single synthetic entry
        // since ReferralWithdrawal itself has no separate log table.
        $adminLogs = AdminLogged::query()
            ->where('subject_type', ReferralWithdrawal::class)
            ->where('subject_id', $withdrawal->id)
            ->get();

        $logs = $adminLogs->sortByDesc('created_at')->values();

        return view('admin.referral.withdrawals.show', [
            'withdrawal' => $withdrawal,
            'referralBalance' => $referralBalance,
            'totalTransactions' => $totalTransactions,
            'logs' => $logs,
        ]);
    }

    /** For method=wallet — credits the user's spendable wallet balance directly. No external transfer needed. */
    public function approveWallet(Request $request, ReferralWithdrawal $withdrawal, WalletService $wallet)
    {
        abort_if($withdrawal->status !== WithdrawalStatus::PENDING, 422, 'This withdrawal is not pending.');
        abort_if($withdrawal->method !== 'wallet', 422, 'This withdrawal is not a wallet withdrawal.');

        $wallet->credit($withdrawal->user, (float) $withdrawal->amount, TransactionType::REFERRAL_BONUS, [
            'currency' => $withdrawal->currency,
            'description' => "Referral withdrawal #{$withdrawal->id} — converted to wallet balance",
        ]);

        $withdrawal->update([
            'status' => WithdrawalStatus::SUCCESS,
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('alert', ['type' => 'success', 'message' => 'Wallet credited.']);
    }

    /**
     * For method=bank — actually sends the money via a Flutterwave transfer.
     * Requires bank_code to have been captured at request time.
     */
    public function approveBank(Request $request, ReferralWithdrawal $withdrawal, FlutterwaveService $flutterwave)
    {
        abort_if($withdrawal->status !== WithdrawalStatus::PENDING, 422, 'This withdrawal is not pending — it may already be processing or completed.');
        abort_if($withdrawal->method !== 'bank', 422, 'This withdrawal is not a bank withdrawal.');

        if (! $withdrawal->bank_code) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'This withdrawal has no bank code on file and cannot be paid automatically. Ask the user to resubmit.',
            ]);
        }

        try {
            $transfer = $flutterwave->initiateTransfer([
                'account_bank' => $withdrawal->bank_code,
                'account_number' => $withdrawal->account_number,
                'amount' => (float) $withdrawal->amount,
                'currency' => $withdrawal->currency,
                'narration' => "Referral withdrawal #{$withdrawal->id}",
                'reference' => $withdrawal->reference,
                'callback_url' => route('flutterwave.webhook'),
            ]);
        } catch (RuntimeException $e) {
            Log::error("Referral withdrawal #{$withdrawal->id} transfer failed to initiate: ".$e->getMessage());

            return back()->with('alert', ['type' => 'error', 'message' => 'Could not initiate transfer: '.$e->getMessage()]);
        }

        // Flutterwave transfers are async — the webhook confirms success/failure
        // and finalizes status. We mark it "processing" here, not "success".
        $withdrawal->update([
            'status' => WithdrawalStatus::PROCESSING,
            'flutterwave_transfer_id' => $transfer['id'] ?? null,
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('alert', ['type' => 'success', 'message' => 'Transfer initiated with Flutterwave. Status will update automatically once it settles.']);
    }

    public function reject(Request $request, ReferralWithdrawal $withdrawal, ReferralService $referralService)
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:255']]);

        abort_if($withdrawal->status !== WithdrawalStatus::PENDING, 422, 'This withdrawal is not pending — it may already be processing or completed.');

        $referralService->credit($withdrawal->user, (float) $withdrawal->amount, "Withdrawal #{$withdrawal->id} rejected — funds returned");

        $withdrawal->update([
            'status' => WithdrawalStatus::FAILED,
            'failure_reason' => $data['reason'],
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('alert', ['type' => 'success', 'message' => 'Withdrawal rejected — funds returned.']);
    }
}