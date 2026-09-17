<?php

namespace App\Types;

class ResellerStatus
{
    const NONE = 'none';         // regular customer, never applied
    const PENDING = 'pending';   // applied, awaiting admin review
    const APPROVED = 'active';
    const REJECTED = 'rejected';

    public static function all(): array
    {
        return [self::NONE, self::PENDING, self::APPROVED, self::REJECTED];
    }
}
