<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use App\Types\TransactionType;
use App\Types\WalletTransactionDirection;
use App\Types\WalletTransactionStatus;
use Illuminate\Http\Request;
use App\Services\ExchangeRateService;

class WalletController extends Controller
{

    public function index(Request $request, ExchangeRateService $rates)
    {
        $query = WalletTransaction::excludingSwitches()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to));

        return view('admin.wallet.index', [
            'transactions' => tap((clone $query)->with('user:id,name,email')->latest()->paginate(20)->withQueryString(),
                fn ($page) => $page->getCollection()->each->withNgnAmounts()),
            'totalTransactions' => (clone $query)->count(),
            'totalDeposits' => $rates->sumConverted((clone $query)->where('type', WalletTransactionDirection::CREDIT), 'amount', 'NGN'),
            'totalDebits' => $rates->sumConverted((clone $query)->where('type', WalletTransactionDirection::DEBIT), 'amount', 'NGN'),
            'pendingDeposits' => (clone $query)->where('status', WalletTransactionStatus::PENDING)->count(),
            'pendingAmount' => $rates->sumConverted((clone $query)->where('status', WalletTransactionStatus::PENDING), 'amount', 'NGN'),
            'completedAmount' => $rates->sumConverted((clone $query)->where('status', WalletTransactionStatus::SUCCESS), 'amount', 'NGN'),
        ]);
    }

    public function show(WalletTransaction $transaction)
    {
        $transaction->load('user');
        $customerBalance = $transaction->user->balanceInNgn();

        $logs = WalletTransaction::where('user_id', $transaction->user_id)->latest()->paginate(15);
        $logs->getCollection()->each->withNgnAmounts();

        $transaction->withNgnAmounts();

        return view('admin.wallet.show', [
            'transaction' => $transaction,
            'customerBalance' => $customerBalance,
            'totalTransactions' => WalletTransaction::where('user_id', $transaction->user_id)->count(),
            'logs' => $logs,
        ]);
    }
    /** Approves a pending manual top-up (e.g. bank transfer submitted for review) — actually credits the wallet now. */
    public function approve(Request $request, WalletTransaction $transaction, WalletService $wallet)
    {
        abort_if($transaction->status !== WalletTransactionStatus::PENDING, 422, 'This transaction is not pending.');

        $wallet->credit($transaction->user, (float) $transaction->amount, TransactionType::TOPUP, [
            'currency' => $transaction->currency,
            'payment_method' => $transaction->payment_method,
            'description' => "Approved: {$transaction->description}",
        ]);

        $transaction->update(['status' => WalletTransactionStatus::SUCCESS]);

        return back()->with('success', 'Top-up approved and wallet credited.');
    }

    public function reject(Request $request, WalletTransaction $transaction)
    {
        abort_if($transaction->status !== WalletTransactionStatus::PENDING, 422, 'This transaction is not pending.');

        $transaction->update([
            'status' => WalletTransactionStatus::FAILED,
            'description' => trim(($transaction->description ?? '')." — Rejected: {$request->input('reason')}"),
        ]);

        return back()->with('success', 'Top-up rejected.');
    }

    public function destroy(WalletTransaction $transaction)
    {
        abort_unless($transaction->status === WalletTransactionStatus::PENDING, 422, 'Only pending (never-credited) transactions can be deleted.');
        $transaction->delete();

        return back()->with('success', 'Transaction record deleted.');
    }

    /** Manual balance adjustment from the customer detail page. */
    public function adjust(Request $request, User $user, WalletService $wallet)
    {
        $data = $request->validate([
            'type' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $purpose = $data['type'] === 'credit' ? TransactionType::ADMIN_CREDIT : TransactionType::ADMIN_DEBIT;

        try {
            $data['type'] === 'credit'
              ? $wallet->credit($user, $data['amount'], $purpose, ['description' => $data['reason'], 'currency' => 'NGN'])
        : $wallet->debit($user, $data['amount'], $purpose, ['description' => $data['reason'], 'currency' => 'NGN']);
        } catch (\App\Services\InsufficientBalanceException $e) {
            return back()->with('error', 'User has insufficient balance for this debit.');
        }

        return back()->with('success', 'Wallet adjusted.');
    }
}
