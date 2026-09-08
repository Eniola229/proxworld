<?php

namespace App\Types;

class AccountStatus
{
    const ACTIVE = 'active';
    const SUSPENDED = 'suspended';
    const BANNED = 'banned';

    public static function all(): array
    {
        return [self::ACTIVE, self::SUSPENDED, self::BANNED];
    }
}
