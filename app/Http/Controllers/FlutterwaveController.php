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
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;

class FlutterwaveController extends Controller
{
    public function __construct(
        protected FlutterwaveService $flutterwave,
        protected WalletService $wallet,
    ) {
    }

    /** Customer lands here after hosted checkout (?status=&tx_ref=&transaction_id=). Only a hint — we re-verify. */
    public function callback(Request $request)
    {
        $txRef = $request->query('tx_ref') ?? $request->query('reference');

        if (! $txRef) {
            return redirect()->route('wallet.index')->with('error', 'Payment reference missing.');
        }

        $redirectRoute = str_starts_with($txRef, 'PXWR-') ? 'reseller.wallet.index' : 'wallet.index';

        if ($request->query('status') === 'cancelled') {
            return redirect()->route($redirectRoute)->with('alert', [
                'type' => 'error',
                'message' => 'Payment was cancelled. You have not been charged.',
            ]);
        }

        $result = $this->verifyAndCreditTopUp($txRef);

        return redirect()->route($redirectRoute)->with('alert', [
            'type' => $result ? 'success' : 'error',
            'message' => $result
                ? 'Your wallet has been funded successfully.'
                : 'We could not confirm this payment yet. If you were charged it will reflect shortly, otherwise contact support.',
        ]);
    }

    public function webhook(Request $request)
    {
        $configuredHash = (string) config('services.flutterwave.secret_hash');

        if ($configuredHash === '') {
            Log::error('Flutterwave webhook rejected: FLW_SECRET_HASH is not configured.');

            return response()->json(['message' => 'Webhook not configured'], 500);
        }

        // v3 sends your Secret Hash as-is in `verif-hash` (newer dashboards may also send an HMAC `flutterwave-signature`; accepted too).
        $verifHash = (string) $request->header('verif-hash');
        $signature = (string) $request->header('flutterwave-signature');

        $valid = ($verifHash !== '' && hash_equals($configuredHash, $verifHash))
            || ($signature !== '' && hash_equals(base64_encode(hash_hmac('sha256', $request->getContent(), $configuredHash, true)), $signature));

        if (! $valid) {
            Log::warning('Flutterwave webhook rejected: missing or invalid signature.');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = (string) ($request->input('event') ?? '');
        $data = (array) $request->input('data', []);

        if (str_starts_with($event, 'transfer')) {
            $this->handleTransferEvent($data);
        } elseif (str_starts_with($event, 'charge')) {
            $this->handleChargeEvent($data);
        }

        return response()->json(['message' => 'ok']);
    }

    protected function handleChargeEvent(array $data): void
    {
        $reference = $data['tx_ref'] ?? null;

        // Only our own references; credit happens only after verifyByReference() confirms with Flutterwave.
        if ($reference && str_starts_with((string) $reference, 'PX')) {
            $this->verifyAndCreditTopUp((string) $reference);
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

    protected function verifyAndCreditTopUp(string $reference): bool
    {
        $lock = Cache::lock("flutterwave:credit:{$reference}", 30);

        try {
            $lock->block(10);
        } catch (LockTimeoutException $e) {
            return WalletTransaction::where('reference', $reference)->exists()
                || ResellerWalletTransaction::where('reference', $reference)->exists();
        }

        try {
            return $this->verifyAndCredit($reference);
        } catch (\Throwable $e) {
            Log::error("Flutterwave top-up {$reference} failed while crediting: ".$e->getMessage());

            return false;
        } finally {
            $lock->release();
        }
    }

    protected function verifyAndCredit(string $reference): bool
    {
        if (WalletTransaction::where('reference', $reference)->exists()) {
            return true;
        }

        if (ResellerWalletTransaction::where('reference', $reference)->exists()) {
            return true;
        }

        $charge = $this->flutterwave->verifyByReference($reference);

        if (! $charge) {
            Log::warning("Flutterwave charge for reference {$reference} not verified successful.");

            return false;
        }

        $meta = $this->flutterwave->getCachedMeta($reference);

        // Cache cleared? Match by the customer email on the Flutterwave-verified transaction (never browser input).
        if (empty($meta) && ! str_starts_with($reference, 'PXWR-')) {
            $email = $charge['raw']['customer']['email'] ?? null;
            $found = $email ? User::where('email', $email)->first() : null;

            if ($found) {
                $meta = ['user_id' => $found->id];
            }
        }

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