<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\InsufficientBalanceException;
use App\Services\WalletService;
use App\Types\TransactionType;
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
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', ['customers' => $customers]);
    }

    public function show(User $user)
    {
        return view('admin.customers.show', [
            'customer' => $user,
            'orders' => $user->orders()->latest()->paginate(10),
            'walletTransactions' => $user->wallet()->latest()->paginate(10),
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
