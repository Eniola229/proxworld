<?php

namespace App\Http\Controllers;

use App\Mail\WalletReceiptMail;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use App\Types\TransactionType;
use App\Types\WalletTransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Flutterwave verification happens server-side against Flutterwave's own
 * /transactions/{id}/verify endpoint — never trust the amount/status from
 * the redirect query string or webhook payload alone, since both can be
 * spoofed. The webhook is the reliable source of truth; the redirect
 * callback is only used for UX (redirecting the user back with a message)
 * and re-verifies independently before crediting anything.
 */
class FlutterwaveController extends Controller
{
    public function __construct(protected WalletService $wallet)
    {
    }

    /** User is redirected here after checkout — verify independently, don't trust query params. */
    public function callback(Request $request)
    {
        $txRef = $request->query('tx_ref');
        $transactionId = $request->query('transaction_id');

        if (! $txRef || ! $transactionId) {
            return redirect()->route('wallet.index')->with('error', 'Payment reference missing.');
        }

        $result = $this->verifyAndCredit($transactionId, $txRef);

        return redirect()->route('wallet.index')->with(
            $result ? 'success' : 'error',
            $result ? 'Your wallet has been funded successfully.' : 'We could not verify this payment. Contact support if you were charged.'
        );
    }

    /** Flutterwave server-to-server webhook — the reliable path. Register this URL in your Flutterwave dashboard. */
    public function webhook(Request $request)
    {
        $signature = $request->header('verif-hash');

        if (! $signature || $signature !== config('services.flutterwave.secret_hash')) {
            Log::warning('Flutterwave webhook rejected: invalid signature.');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = $request->input('data', []);
        $transactionId = $data['id'] ?? null;
        $txRef = $data['tx_ref'] ?? null;

        if ($transactionId && $txRef) {
            $this->verifyAndCredit($transactionId, $txRef);
        }

        return response()->json(['message' => 'ok']);
    }

    protected function verifyAndCredit(string $transactionId, string $txRef): bool
    {
        // Idempotency — never double-credit the same reference, whether the
        // webhook and redirect both fire, or the webhook fires twice.
        if (WalletTransaction::where('reference', $txRef)->exists()) {
            return true;
        }

        $response = Http::withToken(config('services.flutterwave.secret_key'))
            ->get(config('services.flutterwave.base_url')."/transactions/{$transactionId}/verify");

        if (! $response->successful()) {
            Log::error('Flutterwave verification request failed: '.$response->body());

            return false;
        }

        $tx = $response->json('data');

        if (($tx['status'] ?? null) !== 'successful' || ($tx['tx_ref'] ?? null) !== $txRef) {
            Log::warning("Flutterwave transaction {$transactionId} not verified successful.", $tx ?? []);

            return false;
        }

        $userId = $tx['meta']['user_id'] ?? null;
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            Log::error("Flutterwave transaction {$transactionId} has no resolvable user_id in meta.");

            return false;
        }

        $walletTx = $this->wallet->credit($user, (float) $tx['amount'], TransactionType::TOPUP, [
            'reference' => $txRef,
            'currency' => $tx['currency'] ?? 'NGN',
            'payment_method' => 'flutterwave',
            'status' => WalletTransactionStatus::SUCCESS,
            'description' => 'Wallet top-up via Flutterwave',
            'meta' => ['flutterwave_transaction_id' => $transactionId, 'raw' => $tx],
        ]);

        Mail::to($user->email)->send(new WalletReceiptMail($walletTx));

        // First deposit may trigger the referrer's bonus.
        app(\App\Services\ReferralService::class)->checkAndPayBonus($user);
        \App\Models\Referral::where('referred_user_id', $user->id)->update(['has_deposited' => true]);
        app(\App\Services\ReferralService::class)->checkAndPayBonus($user);

        return true;
    }
}
