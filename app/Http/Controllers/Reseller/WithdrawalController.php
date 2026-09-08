<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerWithdrawal;
use App\Services\ResellerProfitService;
use App\Types\ProfitTransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WithdrawalController extends Controller
{
    public function create(Request $request)
    {
        $reseller = $request->user()->reseller;

        return view('reseller.manage.withdraw', [
            'availableBalance' => $reseller->profit_balance,
            'totalProfit' => $reseller->total_profit_earned,
        ]);
    }

    public function store(Request $request, ResellerProfitService $profitService)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:20'],
            'account_name' => ['required', 'string', 'max:100'],
        ]);

        $reseller = $request->user()->reseller;

        try {
            // Reserved immediately — prevents submitting two withdrawal
            // requests against the same funds before admin reviews the first.
            $profitService->debit($reseller, $data['amount'], ProfitTransactionType::WITHDRAWAL_REQUEST, [
                'description' => 'Withdrawal request submitted',
            ]);
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Insufficient available balance.');
        }

        ResellerWithdrawal::create([
            'reseller_id' => $reseller->id,
            'amount' => $data['amount'],
            'currency' => 'NGN',
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'reference' => 'RSW-'.strtoupper(Str::random(12)),
            'status' => 'pending',
        ]);

        return redirect()->route('reseller.dashboard')->with('success', 'Withdrawal request submitted for review.');
    }
}
