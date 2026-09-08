<?php

namespace Database\Seeders;

use App\Types\AdminRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Every admin action in the app should be gated by a permission check
 * (middleware `permission:x` or `$admin->can('x')` in a controller/policy),
 * never by a raw role name check — that's what makes "everything in admin
 * is permission-based" actually true rather than just role-name-based.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'admin';

        $permissions = [
            // Dashboard & reporting
            'dashboard.view',
            'profit.view', // gates the profit card/report specifically — only Super Admin + Finance by default
            'reports.view',

            // Orders
            'orders.view', 'orders.manage', 'orders.refund',

            // Customers
            'customers.view', 'customers.manage', 'customers.suspend',

            // Resellers
            'resellers.view', 'resellers.manage', 'resellers.approve', 'resellers.suspend',
            'reseller-withdrawals.view', 'reseller-withdrawals.process',
            'referral-withdrawals.view', 'referral-withdrawals.process',

            // Providers & pricing
            'providers.view', 'providers.manage',
            'pricing.view', 'pricing.manage',
            'currencies.manage',

            // Wallet / finance
            'wallet.view', 'wallet.adjust',

            // Support
            'tickets.view', 'tickets.manage',

            // Newsletter / blog
            'newsletter.view', 'newsletter.send', 'newsletter.manage',

            // Admin management
            'admins.view', 'admins.manage',
            'roles.manage',

            // System
            'settings.manage',
            'activity-logs.view',
            'api-keys.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guard]);
        }

        $roles = [
            AdminRole::SUPER_ADMIN => $permissions, // everything

            AdminRole::ADMIN => [
                'dashboard.view', 'orders.view', 'orders.manage', 'orders.refund',
                'customers.view', 'customers.manage', 'customers.suspend',
                'resellers.view', 'resellers.manage', 'resellers.approve', 'resellers.suspend',
                'reseller-withdrawals.view', 'reseller-withdrawals.process',
                'referral-withdrawals.view', 'referral-withdrawals.process',
                'providers.view', 'providers.manage', 'pricing.view', 'pricing.manage', 'currencies.manage',
                'wallet.view', 'tickets.view', 'tickets.manage',
                'newsletter.view', 'newsletter.send', 'newsletter.manage',
                'api-keys.view', 'reports.view',
            ],

            AdminRole::MANAGER => [
                'dashboard.view', 'orders.view', 'orders.manage',
                'customers.view', 'customers.manage',
                'resellers.view', 'resellers.manage',
                'providers.view', 'tickets.view', 'tickets.manage',
                'newsletter.view', 'reports.view',
            ],

            // Finance — the other role (besides Super Admin) allowed to see profit numbers.
            AdminRole::ACCOUNTANT => [
                'dashboard.view', 'profit.view', 'reports.view',
                'wallet.view', 'wallet.adjust',
                'reseller-withdrawals.view', 'reseller-withdrawals.process',
                'referral-withdrawals.view', 'referral-withdrawals.process',
                'orders.view', 'customers.view', 'resellers.view',
            ],

            AdminRole::HR => [
                'dashboard.view', 'admins.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
