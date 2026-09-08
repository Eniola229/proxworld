<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    /** For method=wallet — credits the user's spendable wallet balance directly. No external transfer needed. */
    public function approveWallet(Request $request, ReferralWithdrawal $withdrawal, WalletService $wallet)
    {
        abort_if($withdrawal->status === WithdrawalStatus::SUCCESS, 422, 'Already processed.');
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

        return back()->with('success', 'Wallet credited.');
    }

    /**
     * For method=bank — actually sends the money via a Flutterwave transfer.
     * Requires bank_code to have been captured at request time (see ReferralWithdrawalController@resolveAccount).
     */
    public function approveBank(Request $request, ReferralWithdrawal $withdrawal, FlutterwaveService $flutterwave)
    {
        abort_if($withdrawal->status === WithdrawalStatus::SUCCESS, 422, 'Already processed.');
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

        // Flutterwave transfers are async — the webhook (FlutterwaveController::handleTransferEvent)
        // confirms success/failure and finalizes status. We mark it "processing" here, not "success".
        $withdrawal->update([
            'status' => WithdrawalStatus::PROCESSING ?? WithdrawalStatus::APPROVED,
            'flutterwave_transfer_id' => $transfer['id'] ?? null,
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Transfer initiated with Flutterwave. Status will update automatically once it settles.');
    }

    public function reject(Request $request, ReferralWithdrawal $withdrawal, ReferralService $referralService)
    {
        $data = $request->validate(['failure_reason' => ['required', 'string', 'max:255']]);

        abort_if($withdrawal->status === WithdrawalStatus::SUCCESS, 422, 'Already processed.');

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