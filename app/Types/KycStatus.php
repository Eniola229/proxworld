<?php

namespace App\Types;

class KycStatus
{
    public const UNVERIFIED = 'unverified';
    public const PENDING = 'pending';
    public const IN_REVIEW = 'in_review';
    public const VERIFIED = 'verified';
    public const DECLINED = 'declined';

    public static function all(): array
    {
        return [
            self::UNVERIFIED,
            self::PENDING,
            self::IN_REVIEW,
            self::VERIFIED,
            self::DECLINED,
        ];
    }

    public static function label(?string $status): string
    {
        return match ($status) {
            self::VERIFIED => 'Verified',
            self::PENDING => 'Pending',
            self::IN_REVIEW => 'In review',
            self::DECLINED => 'Declined',
            default => 'Not verified',
        };
    }

    /** Theme colour name used with bg-soft-{color} / text-{color}. */
    public static function color(?string $status): string
    {
        return match ($status) {
            self::VERIFIED => 'success',
            self::PENDING => 'info',
            self::IN_REVIEW => 'warning',
            self::DECLINED => 'danger',
            default => 'secondary',
        };
    }
}
