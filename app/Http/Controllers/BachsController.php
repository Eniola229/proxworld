<?php

namespace App\Http\Controllers;

use App\Mail\WalletReceiptMail;
use App\Models\Referral;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\BachsService;
use App\Services\ReferralService;
use App\Services\WalletService;
use App\Types\TransactionType;
use App\Types\WalletTransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BachsController extends Controller
{
    public function __construct(
        protected BachsService $bachs,
        protected WalletService $wallet,
    ) {
    }

    /** Customer lands here after hosted checkout. Only a hint — the webhook does the crediting. */
    public function callback(Request $request, string $reference)
    {
        $credited = WalletTransaction::where('reference', $reference)->exists();

        return redirect()->route('wallet.index')->with('alert', [
            'type' => $credited ? 'success' : 'info',
            'message' => $credited
                ? 'Your wallet has been funded successfully.'
                : 'Payment received. We are confirming it now and your wallet will update shortly. If it does not, contact support.',
        ]);
    }

    public function cancelled()
    {
        return redirect()->route('wallet.index')->with('alert', [
            'type' => 'error',
            'message' => 'Payment was cancelled. You have not been charged.',
        ]);
    }

    public function webhook(Request $request)
    {
        if (! $this->bachs->hasWebhookSecret()) {
            Log::error('Bachs webhook rejected: BACHS_WEBHOOK_SECRET is not configured.');

            return response()->json(['message' => 'Webhook not configured'], 500);
        }

        $valid = $this->bachs->verifySignature(
            $request->getContent(),
            $request->header('X-Bachs-Timestamp'),
            $request->header('X-Bachs-Signature'),
            $request->header('X-Bachs-Signature-V2'),
        );

        if (! $valid) {
            Log::warning('Bachs webhook rejected: missing or invalid signature.');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        if ($request->input('type') === 'collection.succeeded') {
            try {
                $this->handleCollectionSucceeded((array) $request->input('data', []));
            } catch (\Throwable $e) {
                // Non-2xx makes Bachs retry (at-least-once delivery). Crediting is idempotent, so retries are safe.
                Log::error('Bachs collection.succeeded failed: '.$e->getMessage());

                return response()->json(['message' => 'retry'], 500);
            }
        }

        return response()->json(['message' => 'ok']);
    }

    protected function handleCollectionSucceeded(array $data): void
    {
        $reference = (string) ($data['reference'] ?? '');
        $status = strtoupper((string) ($data['status'] ?? ''));

        // Only our own top-ups (ignores virtual-account deposits and anything else on the Bachs account).
        if (! str_starts_with($reference, 'PXB-')) {
            return;
        }

        if (! in_array($status, ['SUCCEEDED', 'OVERPAID'], true)) {
            Log::warning("Bachs charge {$reference} has status {$status}; not credited.", ['data' => $data]);

            return;
        }

        $lock = Cache::lock("bachs:credit:{$reference}", 30);
        $lock->block(10);

        try {
            $this->creditTopUp($data, $reference);
        } finally {
            $lock->release();
        }
    }

    protected function creditTopUp(array $data, string $reference): void
    {
        if (WalletTransaction::where('reference', $reference)->exists()) {
            return;
        }

        $currency = strtoupper((string) ($data['currency'] ?? ''));
        $paid = (float) ($data['amount'] ?? 0);

        if ($currency === '' || $paid <= 0) {
            Log::error("Bachs charge {$reference} has no usable amount/currency.", ['data' => $data]);

            return;
        }

        // Credit what we asked for, never more. Customer-borne fees can make the charged amount higher.
        $expected = $this->bachs->getExpected($reference);

        if ($expected) {
            if ($expected['currency'] !== $currency || $paid + 0.01 < (float) $expected['amount']) {
                Log::error('Bachs amount/currency mismatch', [
                    'reference' => $reference, 'expected' => $expected, 'got' => ['amount' => $paid, 'currency' => $currency],
                ]);

                return;
            }

            $creditAmount = (float) $expected['amount'];
        } else {
            $creditAmount = $paid;
        }

        $meta = $this->bachs->getCachedMeta($reference);
        $userId = $meta['user_id'] ?? ($data['metadata']['user_id'] ?? null);
        $user = $userId ? User::find($userId) : null;

        // Cache cleared and no metadata? Fall back to the email on the signed payload.
        if (! $user && ! empty($data['customer']['email'])) {
            $user = User::where('email', $data['customer']['email'])->first();
        }

        if (! $user) {
            Log::error("Bachs charge {$reference} has no resolvable user.", ['data' => $data]);

            return;
        }

        $walletTx = $this->wallet->credit($user, $creditAmount, TransactionType::TOPUP, [
            'reference' => $reference,
            'currency' => $currency,
            'payment_method' => 'bachs',
            'status' => WalletTransactionStatus::SUCCESS,
            'description' => 'Wallet top-up via Bachs',
            'meta' => [
                'bachs_charge_id' => $data['charge_id'] ?? null,
                'bachs_checkout_id' => $data['checkout_id'] ?? null,
                'raw' => $data,
            ],
        ]);

        try {
            Mail::to($user->email)->send(new WalletReceiptMail($walletTx));
        } catch (\Throwable $e) {
            Log::error("Bachs receipt email failed for {$reference}: ".$e->getMessage());
        }

        Referral::where('referred_user_id', $user->id)->update(['has_deposited' => true]);
        app(ReferralService::class)->checkAndPayBonus($user);
    }
}