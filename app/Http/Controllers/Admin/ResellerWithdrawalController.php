<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResellerWithdrawal;
use App\Services\FlutterwaveService;
use App\Services\ResellerProfitService;
use App\Types\ProfitTransactionType;
use App\Types\WithdrawalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ResellerWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = ResellerWithdrawal::query()
            ->with('reseller:id,panel_name')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reseller-withdrawals.index', ['withdrawals' => $withdrawals]);
    }

    public function show(ResellerWithdrawal $withdrawal)
    {
        return view('admin.reseller-withdrawals.show', ['withdrawal' => $withdrawal->load('reseller.owner')]);
    }

    /** Actually sends the payout via Flutterwave. Funds were already reserved when the request was submitted. */
    public function approve(Request $request, ResellerWithdrawal $withdrawal, FlutterwaveService $flutterwave)
    {
        abort_if($withdrawal->status === WithdrawalStatus::SUCCESS, 422, 'Already processed.');

        if (! $withdrawal->bank_code) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'This withdrawal has no bank code on file and cannot be paid automatically. Ask the reseller to resubmit.',
            ]);
        }

        try {
            $transfer = $flutterwave->initiateTransfer([
                'account_bank' => $withdrawal->bank_code,
                'account_number' => $withdrawal->account_number,
                'amount' => (float) $withdrawal->amount,
                'currency' => $withdrawal->currency,
                'narration' => "Reseller withdrawal #{$withdrawal->id}",
                'reference' => $withdrawal->reference,
                'callback_url' => route('flutterwave.webhook'),
            ]);
        } catch (RuntimeException $e) {
            Log::error("Reseller withdrawal #{$withdrawal->id} transfer failed to initiate: ".$e->getMessage());

            return back()->with('alert', ['type' => 'error', 'message' => 'Could not initiate transfer: '.$e->getMessage()]);
        }

        $withdrawal->update([
            'status' => WithdrawalStatus::PROCESSING ?? WithdrawalStatus::APPROVED,
            'flutterwave_transfer_id' => $transfer['id'] ?? null,
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Transfer initiated with Flutterwave. Status will update automatically once it settles.');
    }

    public function reject(Request $request, ResellerWithdrawal $withdrawal, ResellerProfitService $profitService)
    {
        $data = $request->validate(['failure_reason' => ['required', 'string', 'max:255']]);

        abort_if($withdrawal->status === WithdrawalStatus::SUCCESS, 422, 'Already processed.');

        if ($withdrawal->status !== WithdrawalStatus::FAILED) {
            $profitService->credit($withdrawal->reseller, (float) $withdrawal->amount, ProfitTransactionType::WITHDRAWAL_REJECTED_REFUND, [
                'description' => "Withdrawal #{$withdrawal->id} rejected — funds returned",
            ]);
        }

        $withdrawal->update([
            'status' => WithdrawalStatus::FAILED,
            'failure_reason' => $data['failure_reason'],
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Withdrawal rejected — funds returned to reseller.');
    }
}