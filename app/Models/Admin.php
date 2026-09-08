<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

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

    protected static function booted(): void
    {
        static::creating(function (Admin $admin) {
            $admin->uuid ??= (string) \Illuminate\Support\Str::uuid();
        });
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
}
