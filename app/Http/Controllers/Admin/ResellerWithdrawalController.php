<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResellerWithdrawal;
use App\Services\ResellerProfitService;
use App\Types\ProfitTransactionType;
use App\Types\WithdrawalStatus;
use Illuminate\Http\Request;

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

    /** Marks as paid — funds were already reserved when the request was submitted. */
    public function approve(Request $request, ResellerWithdrawal $withdrawal)
    {
        abort_if($withdrawal->status === WithdrawalStatus::SUCCESS, 422, 'Already processed.');

        $withdrawal->update([
            'status' => WithdrawalStatus::SUCCESS,
            'processed_by' => $request->user('admin')->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Withdrawal marked as paid.');
    }

    public function reject(Request $request, ResellerWithdrawal $withdrawal, ResellerProfitService $profitService)
    {
        $data = $request->validate(['failure_reason' => ['required', 'string', 'max:255']]);

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
