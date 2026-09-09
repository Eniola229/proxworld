<?php

namespace App\Models;

use App\Types\AdminRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasUuids;

    protected $guard_name = 'admin';

    protected $fillable = ['name', 'email', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(AdminInvitation::class);
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_admin_id');
    }

    /** Activity log entries this admin caused — used by the per-admin log viewer. */
    public function actions()
    {
        return \Spatie\Activitylog\Models\Activity::where('causer_type', static::class)->where('causer_id', $this->id);
    }

    /** Convenience — checks the real Spatie permission, not the display `role` column. */
    public function canViewProfit(): bool
    {
        return $this->can('profit.view');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Blade convenience methods — every one below is a thin wrapper around
    // hasRole()/can() so the blades stay readable, but the *actual* access
    // control still lives in routes/admin.php's `permission:` middleware.
    // These only control what's rendered, never what's reachable.
    // ─────────────────────────────────────────────────────────────────────

    /** True role check — used for the couple of screens that are genuinely
     *  Super Admin-only regardless of which individual permissions someone
     *  else happens to also hold (e.g. Providers/Pricing nav items). */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(AdminRole::SUPER_ADMIN);
    }

    public function isAccountant(): bool
    {
        return $this->hasRole(AdminRole::ACCOUNTANT);
    }

    /**
     * No "support" role currently exists in RolesAndPermissionsSeeder
     * (only Super Admin, Admin, Manager, Accountant, HR are seeded), so this
     * will always evaluate false right now — meaning every "!isSupport()"
     * check in the blades currently shows its controls to anyone whose
     * *route* permission allows the action. If you intend a real
     * support-tier admin (view-only on orders/wallet), either seed a
     * 'support' role + assign it, or tell me and I'll swap this to a
     * permission-based check instead (e.g. !can('orders.manage')).
     */
    public function isSupport(): bool
    {
        return $this->hasRole('support');
    }

    public function canManageReferral(): bool
    {
        return $this->can('referral-withdrawals.view');
    }

    public function canManageAdmins(): bool
    {
        return $this->can('admins.manage');
    }

    public function canViewAdminLogs(): bool
    {
        return $this->can('activity-logs.view');
    }

    public function canEditCustomer(): bool
    {
        return $this->can('customers.manage');
    }

    public function canViewCustomerLogs(): bool
    {
        return $this->can('activity-logs.view');
    }

    public function canAdjustBalance(): bool
    {
        return $this->can('wallet.adjust');
    }

    public function canEditOrders(): bool
    {
        return $this->can('orders.manage');
    }

    public function canDeleteOrders(): bool
    {
        return $this->can('orders.manage');
    }

    public function canManageResellerWithdrawals(): bool
    {
        return $this->can('reseller-withdrawals.process');
    }

    public function canManageSupport(): bool
    {
        return $this->can('tickets.manage');
    }

    public function canEditTransactions(): bool
    {
        return $this->can('wallet.adjust');
    }

    public function canDeleteTransactions(): bool
    {
        return $this->can('wallet.adjust');
    }
}