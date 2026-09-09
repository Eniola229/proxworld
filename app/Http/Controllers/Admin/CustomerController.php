<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\InsufficientBalanceException;
use App\Services\WalletService;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use App\Types\WalletTransactionStatus;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = User::query()
            ->withCount(['orders', 'tickets'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($q2) => $q2
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'totalCustomers' => User::count(),
            'todayCustomers' => User::whereDate('created_at', today())->count(),
            'activeCustomers' => User::where('updated_at', '>=', now()->subDays(30))->count(),
        ]);
    }

    public function show(User $user)
    {
        $logs = null;
        if (auth('admin')->user()->canViewCustomerLogs()) {
            $logs = ActivityLog::where(function ($q) use ($user) {
                    $q->where(fn ($q2) => $q2->where('causer_type', User::class)->where('causer_id', $user->id))
                      ->orWhere(fn ($q2) => $q2->where('subject_type', User::class)->where('subject_id', $user->id));
                })
                ->latest()
                ->paginate(15, ['*'], 'logs_page');
        }

        return view('admin.customers.show', [
            'customer' => $user,
            'walletBalance' => $user->balance,

            'recentOrders' => $user->orders()->latest()->paginate(10, ['*'], 'orders_page'),
            'totalOrders' => $user->orders()->count(),
            'completedOrders' => $user->orders()->where('status', OrderStatus::COMPLETED)->count(),
            'pendingOrders' => $user->orders()->where('status', OrderStatus::PENDING)->count(),
            'processingOrders' => $user->orders()->where('status', OrderStatus::PROCESSING)->count(),

            'recentTransactions' => $user->wallet()->latest()->paginate(10, ['*'], 'transactions_page'),
            'totalDeposits' => $user->wallet()
                ->where('type', TransactionType::TOPUP)
                ->where('status', WalletTransactionStatus::SUCCESS)
                ->sum('amount'),
            'totalSpent' => $user->wallet()
                ->where('type', TransactionType::ORDER_DEBIT)
                ->where('status', WalletTransactionStatus::SUCCESS)
                ->sum('amount'),

            'referredUsers' => $user->referrals()->with('referredUser')->latest()->paginate(10, ['*'], 'referrals_page'),
            'totalReferred' => $user->referrals()->count(),
            'depositedCount' => $user->referrals()->where('has_deposited', true)->count(),
            'orderedCount' => $user->referrals()->where('has_ordered', true)->count(),
            'bonusPaidCount' => $user->referrals()->where('bonus_paid', true)->count(),

            'logs' => $logs,
        ]);
    }

    public function edit(User $user)
    {
        return view('admin.customers.edit', ['customer' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'status' => ['required', 'in:active,suspended,banned'],
        ]);

        $user->update($data);

        return back()->with('success', 'Customer updated.');
    }

    public function adjustBalance(Request $request, User $user, WalletService $wallet)
    {
        $data = $request->validate([
            'type' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $purpose = $data['type'] === 'credit' ? TransactionType::ADMIN_CREDIT : TransactionType::ADMIN_DEBIT;

        try {
            $data['type'] === 'credit'
                ? $wallet->credit($user, $data['amount'], $purpose, ['description' => $data['reason']])
                : $wallet->debit($user, $data['amount'], $purpose, ['description' => $data['reason']]);
        } catch (InsufficientBalanceException $e) {
            return back()->with('error', 'Customer has insufficient balance for this debit.');
        }

        return back()->with('success', 'Balance adjusted.');
    }
}