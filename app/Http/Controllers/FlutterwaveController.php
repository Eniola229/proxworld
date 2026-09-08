<?php

namespace App\Http\Controllers;

use App\Mail\WalletReceiptMail;
use App\Models\ReferralWithdrawal;
use App\Models\Reseller;
use App\Models\ResellerWalletTransaction;
use App\Models\ResellerWithdrawal;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\FlutterwaveService;
use App\Services\ReferralService;
use App\Services\ResellerProfitService;
use App\Services\WalletService;
use App\Types\TransactionType;
use App\Types\WalletTransactionStatus;
use App\Types\WithdrawalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FlutterwaveController extends Controller
{
    public function __construct(
        protected FlutterwaveService $flutterwave,
        protected WalletService $wallet,
    ) {
    }

    /** User/reseller is redirected here after the Mono/bank-account authorization page. */
    public function callback(Request $request)
    {
        // v4's redirect query params for this flow aren't documented as reliably
        // as v3's tx_ref/status/transaction_id were, so we treat them as a hint
        // only and always re-verify against our own cached reference -> charge id.
        $txRef = $request->query('reference') ?? $request->query('tx_ref');

        if (! $txRef) {
            return redirect()->route('wallet.index')->with('error', 'Payment reference missing.');
        }

        $result = $this->verifyAndCreditTopUp($txRef);

        $redirectRoute = str_starts_with($txRef, 'PXWR-') ? 'reseller.wallet.index' : 'wallet.index';

        return redirect()->route($redirectRoute)->with('alert', [
            'type' => $result ? 'success' : 'error',
            'message' => $result
                ? 'Your wallet has been funded successfully.'
                : 'We could not verify this payment. Contact support if you were charged.',
        ]);
    }

    public function webhook(Request $request)
    {
        $configuredHash = config('services.flutterwave.secret_hash');
        $signature = $request->header('flutterwave-signature');

        if (! $configuredHash) {
            Log::error('Flutterwave webhook rejected: FLUTTERWAVE_SECRET_HASH is not configured.');

            return response()->json(['message' => 'Webhook not configured'], 500);
        }

        if (! $signature) {
            Log::warning('Flutterwave webhook rejected: missing signature header.');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // v4 changed the signature scheme: it's no longer the secret hash sent
        // verbatim. Flutterwave now computes HMAC-SHA256 over the RAW request
        // body using your secret hash as the key, base64-encodes the digest,
        // and sends that as flutterwave-signature. We must reproduce that
        // exact computation and compare digests — comparing the raw secret
        // directly against the header (the v3 approach) will always fail.
        $computedSignature = base64_encode(
            hash_hmac('sha256', $request->getContent(), (string) $configuredHash, true)
        );

        if (! hash_equals($computedSignature, (string) $signature)) {
            Log::warning('Flutterwave webhook rejected: invalid signature.');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // v4 uses "type" (e.g. "charge.completed", "transfer.completed"),
        // not v3's "event".
        $type = (string) $request->input('type');
        $data = $request->input('data', []);

        if (str_starts_with($type, 'transfer.')) {
            $this->handleTransferEvent($data);
        } elseif (str_starts_with($type, 'charge.')) {
            $this->handleChargeEvent($data);
        }

        return response()->json(['message' => 'ok']);
    }

    protected function handleChargeEvent(array $data): void
    {
        $chargeId = $data['id'] ?? null;
        $reference = $data['reference'] ?? null;

        if ($chargeId && $reference) {
            $this->verifyAndCreditTopUp($reference, $chargeId);
        }
    }

    protected function handleTransferEvent(array $data): void
    {
        $transferId = $data['id'] ?? null;

        if (! $transferId) {
            return;
        }

        $transfer = $this->flutterwave->getTransfer((string) $transferId);

        if (! $transfer) {
            return;
        }

        $reference = $transfer['reference'] ?? null;
        $status = strtoupper((string) ($transfer['status'] ?? ''));

        if (! $reference) {
            return;
        }

        if (str_starts_with($reference, 'RFW-')) {
            $this->resolveReferralTransfer($reference, $status, $transfer);
        } elseif (str_starts_with($reference, 'RSW-')) {
            $this->resolveResellerTransfer($reference, $status, $transfer);
        }
    }

    protected function resolveReferralTransfer(string $reference, string $status, array $transfer): void
    {
        $withdrawal = ReferralWithdrawal::where('reference', $reference)->first();

        if (! $withdrawal || $withdrawal->status === WithdrawalStatus::SUCCESS) {
            return;
        }

        if (in_array($status, ['SUCCESSFUL', 'SUCCEEDED', 'COMPLETED'], true)) {
            $withdrawal->update(['status' => WithdrawalStatus::SUCCESS, 'processed_at' => now()]);

            return;
        }

        if ($status === 'FAILED') {
            app(ReferralService::class)->credit(
                $withdrawal->user,
                (float) $withdrawal->amount,
                "Withdrawal #{$withdrawal->id} failed at Flutterwave — funds returned"
            );

            $withdrawal->update([
                'status' => WithdrawalStatus::FAILED,
                'failure_reason' => $transfer['failure_reason'] ?? $transfer['complete_message'] ?? 'Bank transfer failed.',
                'processed_at' => now(),
            ]);
        }
    }

    protected function resolveResellerTransfer(string $reference, string $status, array $transfer): void
    {
        $withdrawal = ResellerWithdrawal::where('reference', $reference)->first();

        if (! $withdrawal || $withdrawal->status === WithdrawalStatus::SUCCESS) {
            return;
        }

        if (in_array($status, ['SUCCESSFUL', 'SUCCEEDED', 'COMPLETED'], true)) {
            $withdrawal->update(['status' => WithdrawalStatus::SUCCESS, 'processed_at' => now()]);

            return;
        }

        if ($status === 'FAILED') {
            app(ResellerProfitService::class)->credit(
                $withdrawal->reseller,
                (float) $withdrawal->amount,
                \App\Types\ProfitTransactionType::WITHDRAWAL_REJECTED_REFUND,
                ['description' => "Withdrawal #{$withdrawal->id} failed at Flutterwave — funds returned"]
            );

            $withdrawal->update([
                'status' => WithdrawalStatus::FAILED,
                'failure_reason' => $transfer['failure_reason'] ?? $transfer['complete_message'] ?? 'Bank transfer failed.',
                'processed_at' => now(),
            ]);
        }
    }

    protected function verifyAndCreditTopUp(string $reference, ?string $chargeId = null): bool
    {
        if (WalletTransaction::where('reference', $reference)->exists()) {
            return true;
        }

        if (ResellerWalletTransaction::where('reference', $reference)->exists()) {
            return true;
        }

        $charge = $chargeId
            ? $this->flutterwave->getCharge($chargeId)
            : $this->flutterwave->getChargeByReference($reference); // still used by callback(), which only has the reference

        if (! $charge || ($charge['status'] ?? null) !== 'succeeded' || ($charge['reference'] ?? null) !== $reference) {
            Log::warning("Flutterwave charge for reference {$reference} not verified successful.", $charge ?? []);

            return false;
        }

        // v4 doesn't reliably echo the meta we set at charge-creation time
        // back through the webhook or GET /charges/{id} — we cached it
        // ourselves in FlutterwaveService at initiation time. Fall back to
        // $charge['meta'] only in case Flutterwave starts returning it.
        $meta = $this->flutterwave->getCachedMeta($reference) ?: ($charge['meta'] ?? []);

        if (isset($meta['reseller_id'])) {
            return $this->creditReseller($charge, $reference, $meta);
        }

        return $this->creditUser($charge, $reference, $meta);
    }

    protected function creditUser(array $charge, string $reference, array $meta): bool
    {
        $userId = $meta['user_id'] ?? null;
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            Log::error("Flutterwave charge {$charge['id']} has no resolvable user_id in meta.", ['cached_meta' => $meta]);

            return false;
        }

        $walletTx = $this->wallet->credit($user, (float) $charge['amount'], TransactionType::TOPUP, [
            'reference' => $reference,
            'currency' => $charge['currency'] ?? 'NGN',
            'payment_method' => 'flutterwave',
            'status' => WalletTransactionStatus::SUCCESS,
            'description' => 'Wallet top-up via Flutterwave',
            'meta' => ['flutterwave_charge_id' => $charge['id'], 'raw' => $charge],
        ]);

        Mail::to($user->email)->send(new WalletReceiptMail($walletTx));

        $referralService = app(ReferralService::class);
        \App\Models\Referral::where('referred_user_id', $user->id)->update(['has_deposited' => true]);
        $referralService->checkAndPayBonus($user);

        return true;
    }

    protected function creditReseller(array $charge, string $reference, array $meta): bool
    {
        $resellerId = $meta['reseller_id'] ?? null;
        $reseller = $resellerId ? Reseller::find($resellerId) : null;

        if (! $reseller) {
            Log::error("Flutterwave charge {$charge['id']} has no resolvable reseller_id in meta.", ['cached_meta' => $meta]);

            return false;
        }

        $balanceBefore = (float) $reseller->wallet_balance;
        $amount = (float) $charge['amount'];

        ResellerWalletTransaction::create([
            'reseller_id' => $reseller->id,
            'reference' => $reference,
            'type' => 'topup',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceBefore + $amount,
            'currency' => $charge['currency'] ?? 'NGN',
            'payment_method' => 'flutterwave',
            'status' => WalletTransactionStatus::SUCCESS,
            'description' => 'Reseller wallet top-up via Flutterwave',
        ]);

        $reseller->increment('wallet_balance', $amount);

        return true;
    }
}