<?php

namespace App\Types;

/**
 * The simple display/filter label stored on admins.role (matches the admin
 * list filter dropdown). Real access control is enforced by Spatie
 * roles/permissions on the `admin` guard — this column just mirrors the
 * admin's primary Spatie role name for fast display & filtering, and is
 * kept in sync by App\Services\AdminService whenever roles change.
 */
class AdminRole
{
    const SUPER_ADMIN = 'super_admin';
    const ADMIN = 'admin';
    const MANAGER = 'manager';
    const ACCOUNTANT = 'accountant';
    const HR = 'hr';

    public static function all(): array
    {
        return [self::SUPER_ADMIN, self::ADMIN, self::MANAGER, self::ACCOUNTANT, self::HR];
    }
}
