<?php

namespace App\Types;

class TicketStatus
{
    const OPEN = 'open';
    const IN_PROGRESS = 'in_progress';
    const CLOSED = 'closed';

    public static function all(): array
    {
        return [self::OPEN, self::IN_PROGRESS, self::CLOSED];
    }
}
