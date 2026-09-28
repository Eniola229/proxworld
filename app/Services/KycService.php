<?php

namespace App\Services;

use App\Mail\KycVerifiedMail;
use App\Models\User;
use App\Types\KycStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Single place that changes a user's KYC state. Used by both the Didit
 * webhook and the manual status-check fallback, so the "verified" email
 * only ever goes out once, from one place.
 */
class KycService
{
    /** Didit session status -> our users.kyc_status. Null = ignore/no-op. */
    public function map(string $diditStatus): ?string
    {
        return match ($diditStatus) {
            'Approved'                                                  => KycStatus::VERIFIED,
            'Declined'                                                  => KycStatus::DECLINED,
            'In Review'                                                 => KycStatus::IN_REVIEW,
            'Not Started', 'In Progress', 'Resubmitted', 'Awaiting User' => KycStatus::PENDING,
            'Abandoned', 'Expired', 'Kyc Expired'                       => KycStatus::UNVERIFIED,
            default                                                     => null,
        };
    }

    public function apply(string $userId, string $sessionId, string $diditStatus): void
    {
        $new = $this->map($diditStatus);

        if ($new === null) {
            return;
        }

        $verifiedUser = DB::transaction(function () use ($userId, $sessionId, $diditStatus, $new): ?User {
            $user = User::whereKey($userId)->lockForUpdate()->first();

            if (! $user) {
                return null;
            }

            // Already verified: only "Kyc Expired" on the exact session we approved can undo it.
            if ($user->kyc_status === KycStatus::VERIFIED) {
                if ($diditStatus === 'Kyc Expired' && $user->kyc_session_id === $sessionId) {
                    $user->forceFill([
                        'kyc_status'      => KycStatus::UNVERIFIED,
                        'kyc_verified_at' => null,
                    ])->save();
                }
                return null;
            }

            // Ignore a stale event from an older session (a fresh approval is always accepted).
            if ($user->kyc_session_id && $user->kyc_session_id !== $sessionId && $new !== KycStatus::VERIFIED) {
                return null;
            }

            $user->forceFill([
                'kyc_status'      => $new,
                'kyc_session_id'  => $sessionId,
                'kyc_verified_at' => $new === KycStatus::VERIFIED ? now() : null,
            ])->save();

            return $new === KycStatus::VERIFIED ? $user : null;
        });

        if ($verifiedUser) {
            $this->sendVerifiedEmail($verifiedUser);
        }
    }

    /** Customers who belong to a reseller storefront (users.reseller_id set) never get this email. */
    private function sendVerifiedEmail(User $user): void
    {
        if ($user->reseller_id) {
            return;
        }

        try {
            Mail::to($user->email)->send(new KycVerifiedMail($user));
        } catch (\Throwable $e) {
            Log::warning('KYC verified email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }
}