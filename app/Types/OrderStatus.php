<?php

namespace App\Types;

class OrderStatus
{
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const COMPLETED = 'completed';
    const CANCELLED = 'cancelled';
    const REFUNDED = 'refunded';

    public static function all(): array
    {
        return [self::PENDING, self::PROCESSING, self::COMPLETED, self::CANCELLED, self::REFUNDED];
    }

    public static function labels(): array
    {
        return [
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        ];
    }
}
