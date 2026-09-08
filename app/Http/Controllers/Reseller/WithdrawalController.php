<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\ResellerWithdrawal;
use App\Services\FlutterwaveService;
use App\Services\ResellerProfitService;
use App\Types\ProfitTransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;

class WithdrawalController extends Controller
{
    public function __construct(protected FlutterwaveService $flutterwave)
    {
    }

    public function create(Request $request)
    {
        $reseller = $request->user()->reseller;

        return view('reseller.manage.withdraw', [
            'availableBalance' => $reseller->profit_balance,
            'totalProfit' => $reseller->total_profit_earned,
            'totalWithdrawn' => $reseller->withdrawals()->where('status', 'success')->sum('amount'),
            'banks' => $this->flutterwave->getBanks(),
            'withdrawals' => $reseller->withdrawals()->latest()->paginate(10),
        ]);
    }

    /** AJAX endpoint the blade calls before enabling the submit button — confirms the account name matches. */
    public function resolveAccount(Request $request)
    {
        $data = $request->validate([
            'bank_name' => ['required', 'string'],
            'account_number' => ['required', 'digits:10'],
        ]);

        // The frontend only has the bank *name* from the dropdown label; look up its code
        // server-side so we never trust a client-supplied bank_code.
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

    public function store(Request $request, ResellerProfitService $profitService)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['required', 'string', 'max:20'],
            'account_number' => ['required', 'digits:10'],
            'account_name' => ['required', 'string', 'max:100'],
        ]);

        $reseller = $request->user()->reseller;

        try {
            // Reserved immediately — prevents submitting two withdrawal requests against the
            // same funds before admin reviews the first.
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
            'bank_code' => $data['bank_code'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'reference' => 'RSW-'.strtoupper(Str::random(12)),
            'status' => 'pending',
        ]);

        return redirect()->route('reseller.dashboard')->with('success', 'Withdrawal request submitted for review.');
    }
}