<?php

namespace App\Types;

class TicketSenderType
{
    const USER = 'user';
    const ADMIN = 'admin';

    public static function all(): array
    {
        return [self::USER, self::ADMIN];
    }
}
