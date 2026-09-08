<?php

namespace App\Types;

/**
 * Named audience segments for newsletter targeting. Each maps to a scope
 * method on App\Models\User (see User::scopeAudience()) — add a new
 * segment by adding one constant + one scope method, nothing else changes.
 */
class NewsletterAudience
{
    const ALL = 'all';
    const NEW_USERS = 'new_users';           // registered in last 7 days
    const NEVER_ORDERED = 'never_ordered';
    const RESELLERS = 'resellers';
    const INACTIVE_30_DAYS = 'inactive_30_days';

    public static function all(): array
    {
        return [self::ALL, self::NEW_USERS, self::NEVER_ORDERED, self::RESELLERS, self::INACTIVE_30_DAYS];
    }

    public static function labels(): array
    {
        return [
            self::ALL => 'All Users',
            self::NEW_USERS => 'New Users (last 7 days)',
            self::NEVER_ORDERED => 'Never Placed an Order',
            self::RESELLERS => 'Resellers Only',
            self::INACTIVE_30_DAYS => 'Inactive (30+ days)',
        ];
    }
}
